<?php


	Class RKVoters_Client {
	
		function __construct(){
			global $wpdb;
			$this -> wpdb = $wpdb; 
		}
	
		function outputError(){
			// Print last SQL query string
			echo $this -> wpdb->last_query;
	
			// Print last SQL query result
			echo $this -> wpdb->last_result;
	
			// Print last SQL query Error
			echo $this -> wpdb->last_error;
		
		}
	
	
		// get list of available streets
		function getStreetList(){
			$sql = "SELECT * from voters_streets ORDER BY street_name";
			$streetsRaw = $this -> wpdb -> get_results($sql);
			foreach($streetsRaw as $street){
				if($street -> active_voters == 0) continue;
				$streets[] = $street;
			}
			return $streets;
		}
	
	
		// get list of available turfs
		function getTurfList(){
			$sql = "SELECT * from voters_turfs";
			$turfs = $this -> wpdb -> get_results($sql);
			return $turfs;
		}
	
	
		// update turf assignment for street
		function updateTurfAssignment(){
			extract($this -> request);
			$where = array('streetid' => $streetid);
			$update = array('turfid' => $turfid);
			$this -> wpdb -> update('voters_streets', $update, $where);	
			return $this -> getStreetList();
		}
	
	
		// update totals
		function updateTotals(){
			$streets = $this -> getStreetList();
			foreach($streets as $street){
				$sql = 	"SELECT COUNT(*) as total FROM voters " . 
						"where stname='" . $street -> street_name . 
						"' and active=1";

				$street_totals = array(
					'active_voters' => $this -> wpdb -> get_var($sql),
					'contacts' => $this -> wpdb -> get_var($sql . ' and (support_level = 1 or support_level = 2 or support_level = 3)'),
					'supporters' => $this -> wpdb -> get_var($sql . ' and support_level = 1')
				);
				
				$this -> wpdb -> update('voters_streets', $street_totals, 
										array('streetid' => $street -> streetid));
				
			}
			
			$turfs = $this -> getTurfList();
			foreach($turfs as $turf){
			
			
				$sql = 	"SELECT COUNT(*) as total FROM voters v, voters_streets s " . 
						"where v.stname=s.street_name and s.turfid=" . $turf -> turfid .
						" and v.active=1";

				$turf_totals = array(
					'active_voters' => $this -> wpdb -> get_var($sql),
					'contacts' => $this -> wpdb -> get_var($sql . ' and (support_level = 1 or support_level = 2 or support_level = 3)'),
					'supporters' => $this -> wpdb -> get_var($sql . ' and v.support_level = 1')
				);
				
				$this -> wpdb -> update('voters_turfs', $turf_totals, 
										array('turfid' => $turf -> turfid));
				
			}
			
			return array(
				'streets' => $this -> getStreetList(),
				'turfs' => $this -> getTurfList()
			);
		}
	
	
		// get list of people by search criteria
		function get_knocklist(){
			extract($this -> request['listRequest']);
			
			$limit = ' LIMIT 500';
			
			$where = array();
			
			if(isset($support_level) && $support_level != '-'){
				if($support_level == "0"){
					$where[] = 	'(support_level=0 or ' .
								'(support_level = 2 and (bio LIKE "%vsc%" or bio = "")))';
				}
				else if($support_level == 'V'){
					$where[] = 'volunteer="true"';
				}
				else {
					$where[] = 'support_level=' . $support_level;
				}
			}

			if(isset($party) && $party != '-'){
				$where[] = 'enroll="' . $party . '"';
			}
			
			
			if($street_name != 'Select Street...') $where[] = "stname = '$street_name'";
			if($stnum) $where[] = "stnum = '$stnum'";

			if($search_str != '') {
				$where[] = "(firstname LIKE '%$search_str%' or lastname LIKE '%$search_str%'
								or bio LIKE '%$search_str%' or phone  LIKE '%$search_str%'
								or email LIKE '%$search_str%' )";
			}
			
			
			
			switch($type) {
				// case 'All' : break;

				case 'Active' : 
					$where[] = "active=1";
				break;
				
				case 'Volunteers' : 
					$where[] = "volunteer='true'";
				break;
				
				case 'Donors' :
					$where[] = "EXISTS(SELECT * from voters_contacts where 
									voters.rkid=voters_contacts.rkid 
									and voters_contacts.type='Donation')";
				break;
				
				case 'Phones' :
					$where[] = '(phone <> "" OR phone2 <> "")';
					$limit = '';
				break;

				case 'Phones - Open' :
					$where[] = 'phone <> ""';
					$where[] = 'closed = 0';
					$limit = '';
				break;

				
				case 'Phones - Not Called' :
					$where[] = 'phone <> ""';
					$where[] = 'not exists (select * from voters_contacts vc where 
									vc.rkid = voters.rkid and vc.type="Phone Call")';
					$limit = '';
				break;
				
				case 'Phones (Not Anchor) - Not Called' :
					$where[] = 'phoneType = ""';					
					$where[] = 'phone <> ""';
					$where[] = 'not exists (select * from voters_contacts vc where 
									vc.rkid = voters.rkid and vc.type="Phone Call")';
					$limit = '';
				break;
				
				case 'Phones - Called' :
					$where[] = 'phone <> ""';
					$where[] = ' exists (select * from voters_contacts vc where 
									vc.rkid = voters.rkid and vc.type="Phone Call")';
					$limit = '';
				break;
				
				case 'Need Postcards' :
					$where[] = 'stnum != 0';
					$where[] = '(support_level=1 or support_level=2)';
					$where[] = 'bio NOT LIKE "%vsc%" and bio <> ""';
					$where[] = 'not exists (select * from voters_contacts vc where 
									vc.rkid = voters.rkid and vc.type="Sent Post Card")';
				break;
				
				case 'Sent Postcards' :
					$limit = '';				
					$where[] = ' exists (select * from voters_contacts vc where 
									vc.rkid = voters.rkid and vc.type="Sent Post Card")';
				break;
				
				case 'Seniors - Phones - Not Called' :
					$limit = '';				
					$where[] = 'not exists (select * from voters_contacts vc where 
									vc.rkid = voters.rkid and vc.type="Phone Call")';
					$where[] = 'phone <> ""';
					$where[] = 'yob < 1950';
					$where[] = 'yob <> 0';
					$where[] = 'phoneType <> "D1"';
				break;
				
				case 'Seniors - Phones' :
					$limit = '';				
					$where[] = 'phone <> ""';
					$where[] = 'yob < 1950';
					$where[] = 'yob <> 0';
				break;
				
				case 'Active Under 35' :
					$limit = '';				
					$where[] = 'yob > 1980';
					$where[] = 'active=1';
					$where[] = 'votedin2011=1';
					$where[] = 'votedin2013=1';					
				break;
				
				case 'Active Under 35 - with phones' :
					$limit = '';				
					$where[] = 'yob > 1980';
					$where[] = 'active=1';
					$where[] = 'votedin2011=1';
					$where[] = 'votedin2013=1';	
					$where[] = 'phone<>""';	
				break;
				
				case 'West End - Super - No Contact' :
					$limit = '';				
					$where[] = 'votedin2011=1';
					$where[] = 'votedin2013=1';	
					$where[] = 'phone=""';
					$where[] = 'support_level=0';
				
				break;
				
			}
			
			
			if(count($where) == 0) return array();
			

			// MAY NOT WANT TO GET *ALL* FIELDS
			$sql = "SELECT * FROM voters 
					WHERE " . implode(' and ', $where) .
					" ORDER BY stname, stnum, unit, lastname" . $limit;
			
					
			$knocklist = $this -> wpdb -> get_results($sql);
			foreach($knocklist as $index => $person){
				foreach($person as $field => $value){
					$knocklist[$index] -> $field = stripSlashes($value);
				}
			}
			
			// LIMIT TO WEST END
			if($type == 'West End - Super - No Contact'){
				$sql = 'select street_name from voters_streets where turfid=5 or turfid=6';
				$streets = $this -> wpdb -> get_results($sql);
				foreach($streets as $street){
					$streetHash[$street -> street_name] = true;
				}
				foreach($knocklist as $k => $person){
					if($streetHash[$person -> stname]){
						$response[] = $person;
					}
				}
				return $response;
			}
			
			return $knocklist;
		}

	
		// get call list
		function get_calllist(){
			$this -> request['listRequest']['hasPhone'] = true;
			
			$list = $this -> get_knocklist();
			
			return $list;
		
		}
		
	
		// send post cards
		function send_postcards(){
			
			// get everybody who has a postcard that needs to be sent
			$sql = 'select * from voters v where 
					exists (select * from voters_contacts vc where vc.rkid = v.rkid and vc.type="Post Card")
					and
					not exists (select * from voters_contacts vc where vc.rkid = v.rkid and vc.type="Sent Post Card")';
					
			$recipients = $this -> wpdb -> get_results($sql);
			
			foreach($recipients as $recipient){
				$contact = array(
					'datetime' => date("Y-m-d H:i:s"),
					'type' => 'Sent Post Card',
					'rkid' => $recipient -> rkid
				);
			
				$response = $this -> wpdb -> insert('voters_contacts', $contact);
			}
			
			return array(
				"knocklist" => $this -> get_knocklist()
			);
			
		}


		// get local supporter email list
		function getLocalSupporterEmails(){
			$sql = 'SELECT email from voters where support_level=1 and (city="Portland" or city="") and email <> ""';
			$list = $this -> wpdb -> get_results($sql);
			$response = '';
			foreach($list as $person){
				$list .= $person -> email . ', ';
			}
			return $list;		
		}
		
		
		// get mailing list
		function getMailingList(){
			$sql = 'SELECT * FROM `voters`
					WHERE ( support_level=0 OR support_level=2 )
					AND votedin2013=1 ORDER BY stname,stnum,unit';
			$list = $this -> wpdb -> get_results($sql);
			
			$oldAddress = false;
			$index = -1;
			
			
			/* -- KEEPS NAMES, BUNDLES BY ADDRESS
			foreach($list as $address){

				// address
				$unit = ($address -> unit != '') ? ' ' . $address -> unit : '';
				$address -> address = 	$address -> stnum . ' ' . $address -> stname . $unit . ', ' . 
										$address -> city . ', ' . $address -> state . ' ' . $address -> zip;
				$address -> name = strtoupper($address -> firstname . ' ' . $address -> lastname);
				
				// check for match
				if(	$oldAddress && 
					$address -> address == $oldAddress -> address &&
					$address -> stname != "CONGRESS ST" &&
					$address -> stname != "STATE ST"){
					//  &&  $address -> lastname == $oldAddress -> lastname){
						$response[$index]['name'] .= ' and ' . $address -> name;
				}
				else {
					$index++;
					$response[$index] = array(
						'name' => $address -> name,
						'address' => $address -> address,
						'addr1' => $address -> stnum . ' ' . $address -> stname . $unit,
						'addr2' => $address -> city . ', ' . $address -> state . ', ' . $address -> zip
					);

				}
				$oldAddress = $address;
			}
			*/
			
			// de-dupe by address
			foreach($list as $address){
				$unit = ($address -> unit != '') ? ' ' . $address -> unit : '';
				$addr1 = $address -> stnum . ' ' . $address -> stname . $unit;
				$response[$addr1] = array(
					'addr1' => $addr1,
					'addr2' => $address -> city . ', ' . $address -> state . ', ' . $address -> zip
				);
			}
			
			
			return $response;
		}
	
		// get person's full record
		function getFullPerson(){			
			extract($this -> request);
			$sql = "SELECT * FROM voters WHERE rkid = '$rkid'";
			$person = (array) $this -> wpdb -> get_row($sql);
			foreach($person as $k => $v){
				$person[$k] = stripSlashes($v);
			}
			
			// get contacts
			$sql = "SELECT * FROM voters_contacts WHERE rkid = '$rkid' ORDER BY datetime desc";
			//echo $sql;
			$contactsRaw = $this -> wpdb -> get_results($sql);
			foreach($contactsRaw as $contact){
				$contact -> note = stripSlashes($contact -> note);
				$person['contacts'][] = $contact;
			}
			
			// get other people at that number
			$person['neighbors'] = array();
			if($person['phone'] != ''){
				$sql = "SELECT * FROM voters WHERE phone = '" . $person['phone'] . "' and id <> " . $person['id'];
				//echo $sql;
				$sameNumber = $this -> wpdb -> get_results($sql);
				foreach($sameNumber as $contact){
					$contact -> bio = stripSlashes($contact -> bio);
					$contact -> firstname = '(p) ' . $contact -> firstname;
					$person['neighbors'][$contact -> id] = $contact;
				}
			}
			
			
			// get other people at the same address
			if($person['stname'] != ''){
				$sql = 	"SELECT * FROM voters WHERE 
						stnum = '{$person['stnum']}' 
						and stname = '{$person['stname']}'
						and unit = '{$person['unit']}'
						and id <> {$person['id']}";
				//echo $sql;

				$housemates = $this -> wpdb -> get_results($sql);
				foreach($housemates as $contact){
					$contact -> bio = stripSlashes($contact -> bio);
					$contact -> firstname = '(a) ' . $contact -> firstname;
					if(!$person['neighbors'][$contact -> id]){
						$person['neighbors'][$contact -> id] = $contact;
					}
				}
			}
			
			if(count($person['neighbors']) == 0) $person['neighbors'] = false;
			
			return $person;
		}
	
	
		// create person
		function addPerson(){
			extract($this -> request);
			$this -> wpdb -> insert('voters', $person);	
			echo  $this -> wpdb->last_error;
			return $this -> get_knocklist();
		}
	
		// update person
		function updatePerson(){
			extract($this -> request);
			$where = array('rkid' => $rkid);
			$update = $person;
			unset($update['address']);
			unset($update['age']);
			unset($update['contacts']);
			unset($update['residentLabel']);
			unset($update['neighbors']);
			unset($update['$$hashKey']);				
								
			
			$this -> wpdb -> update('voters', $update, $where);	
			
			
			/* TEST BLOCK
				// Print last SQL query string
				echo $this -> wpdb->last_query;
				echo $this -> wpdb->last_result;
				echo $this -> wpdb->last_error;			
				exit;
			*/
			
						
			return $this -> getFullPerson($person['rkid']);
		}
	
	
		// add contact
		function recordContact(){
		
		
			extract($this -> request);
			if(!isset($contact['datetime']) || $contact['datetime'] == ''){
				$contact['datetime'] = date("Y-m-d H:i:s");
			}
			else {
				$datetime = strtotime($contact['datetime']);
				$contact['datetime'] = date("Y-m-d H:i:s", $datetime);
			}
			
			$user = wp_get_current_user();
			$contact['userName'] = $user -> user_login;
			
			
			if($contact['type'] == 'Phone Call' && $person['phone'] != '') {
				
				$sql = "SELECT * FROM voters WHERE phone = '" . $person['phone'] . "'";
				$sameNumber = $this -> wpdb -> get_results($sql);
				//$this -> request['person']['callcount']++;

				print_r($contact);

				foreach($sameNumber as $target){
					$contact['rkid'] = $target -> rkid;
					$response = $this -> wpdb -> insert('voters_contacts', $contact);
				}
			}
			else {
				
				$response = $this -> wpdb -> insert('voters_contacts', $contact);
			}
			
			$this -> updatePerson();

			
			
//			print_r($contact);			
//			$this -> outputError();
			
			return $this -> getFullPerson($person['rkid']);
		}
		
		// delete contact
		function deleteContact(){
			extract($this -> request);
			$where = array('vc_id' => $vc_id);
			$this -> wpdb -> delete( 'voters_contacts', $where);
			return $this -> getFullPerson($rkid);
		}
	
	
		// remove person
		function removePerson(){
			extract($this -> request);
			$where = array('rkid' => $rkid);
			$this -> wpdb -> delete('voters', $where);						
			return array(
				"support_level" => "deleted",
				"knocklist" => $this -> get_knocklist()
			);
		}
	
	
		// drop lit bomb
		function litBomb(){
			extract($this -> request);
			
			if($date != ''){
				$datetime = strtotime($date);
				$datestr = date("Y-m-d H:i:s", $datetime);
			}
			else {
				$datestr = date("Y-m-d H:i:s");
			}
			
			foreach($rkids as $vid){
				$contact['datetime'] = $datestr;
				$contact['rkid'] = $vid;
				$contact['type'] = 'Lit Drop';				
				$this -> wpdb -> insert('voters_contacts', $contact);
			}
			return $this -> get_knocklist();		
		}
	
		
		// get contacts with emails
		function getContactsWithEmails(){
			$sql = 'select firstname, lastname, stname, city, state, zip, yob, email, phone, bio
					from voters where email <> ""';
			$list = $this -> wpdb -> get_results($sql, 'ARRAY_A');
			return $list;
			
		}
		
		
		// get donations
		function getDonations(){
			$sql = 'SELECT v.firstname, v.lastname, v.rkid, vc.amount, v.city, vc.datetime
					FROM voters v
					INNER JOIN voters_contacts vc ON v.rkid = vc.rkid
					WHERE vc.type = "Donation"
					ORDER BY vc.datetime';

			$results = $this -> wpdb -> get_results($sql);
			$donors = [];
			$prev_id = 0;
			
			$donSets[0]['title'] = 'Non-Local';
			$donSets[1]['title'] = 'Local';		
			$donSets[2]['title'] = 'Small';	

			foreach($results as $r){
			
				if($r -> amount <= 50){
					$setIndex = 2;
				}
				else {
					$setIndex = (strtoupper($r -> city) == 'PORTLAND') ? 1 : 0;
					foreach($r as $k => $v){
						if($k == 'firstname' || $k == 'lastname'){
							$r -> $k = stripSlashes(strtoupper($v));
						}
					}
				}
				
				$datetime = strtotime($r -> datetime);
				$r -> datetime = date("M j", $datetime);
				
				$donSets[$setIndex]['donors'][] = $r;
				$donSets[$setIndex]['total'] += $r -> amount;
			}

			return $donSets;
		}
		
		
		// export donations
		function exportDonations(){
			$sql = 'SELECT v.*,
					vc.amount, v.city, vc.datetime
					FROM voters v
					INNER JOIN voters_contacts vc ON v.rkid = vc.rkid
					WHERE vc.type = "Donation"
					ORDER BY vc.datetime';

			$results = $this -> wpdb -> get_results($sql);
			$response = array();				

			foreach($results as $r){
				
				foreach($r as $k => $v){
					$r -> $k = stripSlashes(strtoupper($v));
				}
				
				$unit = ($r -> unit != '') ? ' ' . $r -> unit : '';
				$addr1 = $r -> stnum . ' ' . $r -> stname . $unit;
				if($addr1 == 0) $addr1 = '';
				
				$type = ($r -> firstname == 'ROBERT' && $r -> lastname == 'KOROBKIN') ? 1 : 2;
				
				$total += $r -> amount;
				
				if($r -> amount <= 50) {
					$cash += $r -> amount;
					continue;
				}
				
				$donor = array(
					'Date' => date("M j", strtotime($r -> datetime)),
					'First' => $r -> firstname, 
					'Last' => $r -> lastname,
					'Street' => $addr1,
					'City' => $r -> city,
					'State' => $r -> state,
					'Zip' => $r -> zip,
					'Profession' => $r -> profession,
					'Employer' => $r -> employer,
					'Amount' => $r -> amount,
					'Type' => $type
				);

				$response[] = $donor;				
				
			}

			$response[] = array(
				'Date' => '',
				'First' => 'DONATIONS UNDER $50', 
				'Last' => '',
				'Street' => '',
				'City' => '',
				'State' => '',
				'Zip' => '',
				'Profession' => '',
				'Employer' => '',
				'Amount' => $cash,
				'Type' => 8
			);
			
			
			$response[] = array(
				'Date' => '',
				'First' => 'TOTAL DONATIONS', 
				'Last' => '',
				'Street' => '',
				'City' => '',
				'State' => '',
				'Zip' => '',
				'Profession' => '',
				'Employer' => '',
				'Amount' => $total,
				'Type' => ''
			);
			
			return $response;
		}
	
	}