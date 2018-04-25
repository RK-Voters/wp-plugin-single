

var app = angular.module('RKVApp', ['ui.bootstrap']);

app.controller('RKVCtrl', ['$scope', '$http', '$sce', '$rootScope', '$window', '$uibModal',
	function($scope, $http, $sce, $rootScope, $window, $uibModal){
		
		// INIT STATE
		var $ = jQuery;
		$scope.init = function(){			
			$rootScope.appScope = $scope;
			$scope.people = {};
		
			$rootScope.query = {

				print_mode : false,

				firstname : "",
				lastname : "",
				support_level : "Support Level",
				enroll : "D",
				sex : "Gender",
				age_range: "Age",
				
				active: true,
				has_phone: false,
				never_called: false,

				volunteer: false,
				wants_sign: false,
				host_event: false,

				stnum: "",
				stname: "",
				city: "",
				county: "County",
				region: "Region",
				turf: ""
			}

			$scope.map = {
				root : 	'https://www.google.com/maps/embed/v1/place?' +
						'key=AIzaSyC6MLx8c1eQORx3uTNmL5RwXY761YSXaVs'
			}
			
			
			$rootScope.contactType = 'Phone Call';
			$rootScope.callstatus = 'Connection';
			$scope.merging = false;
			$scope.merge_person = false;
		}			
		$scope.init();



		

		
		// DATA MODEL
		
		
		$scope.load_knocklist = function(data){
			$scope.knocklist = {people: [], addresses: [], contacts: []};
			$scope.contactList = {};
			$scope.contactList.length_label = data.length + ' People';

			
			var current_addr = { address : '', residents : []};
			
			
			$.each(data, function(person_index, person){
				$scope.load_person(person);
				$scope.knocklist.people.push(person);

				if(person.support_level != 0) $scope.knocklist.contacts.push(person);
				
				// load address block
				var addr = person.stnum + ' ' + person.stname  + ", " + person.city + ", " + person.state + ' ' + person.zip;
				if(addr != current_addr.address){
					if(current_addr.address != '') {
						$scope.knocklist.addresses.push(current_addr);
					}
					current_addr = { 
						address : addr, 
						stname : person.stname,
						stnum: person.stnum,
						residents : []
					};
				}
				current_addr.residents.push(person);
			});
			if(current_addr.address != '') {
				$scope.knocklist.addresses.push(current_addr);
			}
			
			
			// build multi-street object
			if($scope.viewMode == 'multi-sheet'){
				$scope.street_sets = [];
				var current_street = 'x';
				var street_index = 0;
				$.each($scope.knocklist.addresses, function(index, address){
					if(address.stname == '') return;
					if(address.stname != current_street){
						street_index++;
						current_street = address.stname;
						$scope.street_sets[street_index] = {
							street_name : address.stname,
							safeUrl : $sce.trustAsResourceUrl($scope.map.root + 
										'&q=' + address.stname + '+PORTLAND+ME'),
							addresses : []
						}
					}
					$scope.street_sets[street_index].addresses.push(address);
				});
			}

			
		}

		$scope.updateList = function(person){
			$scope.person = person;
			person = $scope.load_person(person);
			$scope.featured_person = person;

			var p = $scope.knocklist.people;
			p[$scope.selected_index] = person;
			$scope.load_knocklist(p);

			return person;
		}

		$scope.removePersonFromList = function(){

			var p = $scope.knocklist.people;
			p.splice($scope.selected_index, 1);
			$scope.load_knocklist(p);

		}

		$scope.load_person = function(person){
			var yob = person.dob.split('-')[0];
			person.age = (yob) ? 2018 - yob : '';
			person.address = person.stnum + ' ' + person.stname;
			person.residentLabel = '';
			if(person.unit != '') {
				person.address += ' - ' + person.unit;
				person.residentLabel += person.unit + ' - ';
			}
			person.residentLabel += person.firstname + ' ' + person.lastname + 
									' - ' + person.enroll + ' - ' + person.age + person.sex;
			
			person.residentLabel = person.residentLabel.toUpperCase();
			
			if(person.votedin2011 == 1) {
				
				person.residentLabel += '*';
			}
			if(person.votedin2013 == 1) person.residentLabel += '*';				
			
			if(!('active' in person)) person.active = true;
			
			var cb_fields = ["volunteer", "wants_sign", "host_event", "has_signed"];
			$.each(cb_fields, function(i, f){ 
				person[f] = (person[f] == "1" || person[f] == "true") ? "true" : '';
			});

			
			if(!(person.rkid in $scope.people)){
				$scope.people[person.rkid] = {};
			}
			
			for(var field in person){
				$scope.people[person.rkid][field] = person[field];
			}

			if(person.neighbors){
				$.each(person.neighbors, function(k, neighbor){
					$scope.load_person(neighbor);
				})
			}

			return person;
			
		}

		$scope.markVoters = function(){
			$rootScope.query.enroll = "Party";
			$rootScope.query.active = false;
			$rootScope.viewMode = "mark_voters";
			$scope.marker_mode = "control";

			$scope.selected_control = 0;
			

			$scope.selected_index = 0;

			var request = {
				api : 'toggleSigned',
			}
			$rootScope.appScope.runApi(request, function(status){
				$scope.signed_tally = status.tally;
			});


			$( "body").unbind( "keydown" );
			$('body').keydown(function(e){

				var k = e.which;


				if(k == 37 || k == 39) {

					if(!$scope.knocklist) return;

					// LOAD LIST MODE - SELECT FIRST ONE
					if($scope.marker_mode == "control") {
						
						$('input').blur();
						$scope.marker_mode = "list";
						$scope.selected_index = 0;
						$('#person_' + $scope.knocklist.people[$scope.selected_index].rkid).addClass('selected');

					}

					// LOAD CONTROL MODE - SELECT FIRST ONE
					else if($scope.marker_mode == "list") {
						$scope.marker_mode = "control";
						$('#1_input').focus();
						$scope.selected_control = 1;

						$('#person_' + $scope.knocklist.people[$scope.selected_index].rkid).removeClass('selected');
						$scope.selected_index = -1;

					}

					e.preventDefault();

					return;
				}

				switch(k){
				
					
					// go down
					case 40 : 

						if($scope.marker_mode == "control"){
							$scope.selected_control++;
							if($scope.selected_control == 6) $scope.selected_control = 1;
							$('#' + $scope.selected_control + "_input").focus();
							e.preventDefault();
						}

						// iterate down the list of results
						else if($scope.marker_mode == "list"){
							$('#person_' + $scope.knocklist.people[$scope.selected_index].rkid).removeClass('selected');
							$scope.selected_index++;
							if($scope.selected_index == $scope.knocklist.people.length) $scope.selected_index = 0;
							$('#person_' + $scope.knocklist.people[$scope.selected_index].rkid).addClass('selected');
							e.preventDefault();	
						}
						
					break;


					// go up
					case 38 : 
						if($scope.marker_mode == "control"){
							$scope.selected_control--;
							if($scope.selected_control == 0) $scope.selected_control = 5;
							$('#' + ($scope.selected_control) + "_input").focus();
							e.preventDefault();
						}

						// iterate down the list of results

						else if($scope.marker_mode == "list"){
							$('#person_' + $scope.knocklist.people[$scope.selected_index].rkid).removeClass('selected');
							$scope.selected_index--;
							if($scope.selected_index == -1) $scope.selected_index = $scope.knocklist.people.length - 1;
							$('#person_' + $scope.knocklist.people[$scope.selected_index].rkid).addClass('selected');
							e.preventDefault();
						}

					break;


					// enter key - search
					case 13 :

						// if on the controls, execute the search
						if($scope.marker_mode == "control"){
							$scope.search();
							e.preventDefault();
						}

						// if on the results, toggle the petition status
						else if($scope.marker_mode == "list"){

							var person = $scope.knocklist.people[$scope.selected_index];
							person.has_signed = (person.has_signed == 1 || person.has_signed == "true") ? 0 : 1;
							person.support_level = (person.support_level != 0) ? person.support_level : 3;

							var request = {
								api : 'toggleSigned',
								rkid : person.rkid,
								has_signed : person.has_signed,
								support_level : person.support_level
							}
							$rootScope.appScope.runApi(request, function(status){
								$scope.signed_tally = status.tally;
							});

							e.preventDefault();
						}

						
					break;


				}
			})
		}

		$scope.updateMarker = function(e){
			var id = parseInt(e.target.id.split('_')[0]);
			$scope.selected_control = id;
		}

		$scope.reverse = function(){
			$scope.knocklist.people.reverse();
			$scope.knocklist.addresses.reverse();
		}



		// API
		$scope.getAddress = function(address){
			$scope.listRequest = {
				street_name : address.stname,
				stnum : address.stnum
			}
			$scope.search();
		}
		
		$scope.search = function(){
			var request = {
				api: 'get_voterlist',
				listRequest: $rootScope.query
			}
			$scope.runApi(request, function(response){
				$scope.load_knocklist(response);
				if($scope.query.print_mode) $scope.printList();
			});
		}

		
		
		// UI CONTROLS
		$scope.loadComponent = function(componentName){
			$scope.component = componentName;
			if(componentName == 'report'){
				$scope.viewMode = 'fundraising';
				$scope.updateReportMode('fundraising');
			}
			if(componentName == 'knocklist'){
				$scope.showMap = -1;
				$rootScope.viewMode = 'knocknotes';				
			}
		}
		
		$scope.updateMap = function(street_name){
			if($scope.showMap == 1){
				var street_path = street_name + '+PORTLAND+ME';
				$scope.map.safeUrl = $sce.trustAsResourceUrl($scope.map.root + '&q=' + street_path);
			}
		}
		
		$scope.toggleMap = function(){
			$scope.showMap *= -1;
			if($scope.showMap == 1){
				$scope.updateMap($scope.listRequest.street_name);
			}
		}



		// PRINT LIST
		$scope.printList = function(){
			$('#hiddenForm').html(
				'<textarea name="payload">' + JSON.stringify($scope.knocklist.addresses) + '</textarea>' +
				'<textarea name="query">' + JSON.stringify($scope.query) + '</textarea>' +
				'<input name="list_size" value="' + $scope.knocklist.people.length + '" />' +
				'<input name="api" value="print_list" />').submit();
		}




		// MERGE FUNCTION
		$scope.startMerge = function(person){
			$scope.merge_person = person;
			$scope.merging = true;
			$('#merge_' + person.rkid).addClass('active_merging');
		}



		// API
		$scope.runApi = function(request, callback){
			request.access_token = rkvoters_config.access_token;
			request.campaign_slug = rkvoters_config.campaign_slug;
			request.user_name = rkvoters_config.user_name;
			$http({
				method: 'POST',
				url: rkvoters_config.api_url + "app.php",
				data: request
			}).then(
				function successCallback(response) {
					if(!("error" in response.data)){
						callback(response.data);
						return;
					}
					$scope.handleApiError(response);
				},
				function errorCallback(response) {
					$scope.handleApiError(response);
				}
			);
		}

		$scope.handleApiError = function(response){
			console.log(response);
		}

		
		$scope.openPerson = function(person){

			var goAhead = true;

			if($scope.merging){
				if(confirm("Merge " + person.firstname + ' ' + person.lastname + ' with ' + $scope.merge_person.firstname + ' ' + $scope.merge_person.lastname + '?')){
					
					goAhead = false;

					var request = {
						api : 'merge_people',
						contact : $scope.merge_person,
						voter : person,
						listRequest: $scope.query
					};
					$scope.runApi(request, function(list){
						$scope.load_knocklist(list);
						$scope.merging = false;
						$scope.merge_person = false;
						$('.merge_label').removeClass('active_merging');
					});
				}
				else {
					$scope.merging = false;
					$scope.merge_person = false;
					$('.merge_label').removeClass('active_merging');
					return false;
				}
			}

			if(!goAhead) return false;
			
			if('knocklist' in $scope){
				$.each($scope.knocklist.people, function(index, row){
					if(row.rkid == person.rkid){
						$scope.selected_index = index;			
					}
				});
			}
				
			var request = {
				api : 'getFullPerson',
				rkid : person.rkid
			}

			$scope.runApi(request, function(person){
				$scope.load_person(person);
				$scope.featured_person = person;					
				$uibModal.open({
					templateUrl: rkvoters_config.template_dir + 'modal-person.php',
					controller: 'FeaturePersonCtrl',
				});			
			});
		}
		
		$scope.openListManager = function(){								
			$uibModal.open({
				templateUrl: rkvoters_config.template_dir + 'modal-listmgr.php',
				controller: 'ListManagerCtrl',
			});			
		}
		
		$scope.openPersonAdder = function(){
			$rootScope.mode = 'Add';
			$uibModal.open({
				templateUrl: rkvoters_config.template_dir + 'modal-personeditor.php',
				controller: 'PersonAdderCtrl'
			});		
		}
		
		$scope.toggleStreetSet = function(turf, dontupdate){
			if(turf.state == 'closed'){
				turf.state = 'open';
				turf.toggle_command = 'close';
			}
			else {
				turf.state = 'closed';
				turf.toggle_command = 'open';
			}
		}


		$scope.openSearchForm = function(){
			$rootScope.mode = 'Search';
			$uibModal.open({
				templateUrl: rkvoters_config.template_dir + 'modal-search.php',
				controller: 'SearchFormCtrl'
			});		
		}

		$scope.updateReportMode = function(viewMode){
			if(viewMode == 'fundraising'){
				var request = {
					api : 'getDonations'
				}

				$scope.runApi(request, function(donationSets){
					$scope.donationSets = donationSets;
				});
			}
			if(viewMode == "mailchimp"){
				$('#hiddenForm2').attr('action', rkvoters_config.api_url + 'mailchimp.php').submit();

			}
		}


		$scope.downloadDonors = function(){

			var donors = [];
			for(var i = 0; i < $scope.donationSets.length; i++){
				for(var j = 0; j < $scope.donationSets[i].donors.length; j++){
					donors.push($scope.donationSets[i].donors[j])
				}
			}

			$('#hiddenForm').html(
				'<textarea name="payload">' + JSON.stringify(donors) + '</textarea>' +
				'<input name="api" value="export_donors" />').submit();
		}
	
		// AND FIRE!!!	
		$scope.loadComponent('knocklist');

	}
]);

app.controller('ListManagerCtrl', 
	['$scope', '$rootScope', '$uibModal',
		function($scope, $rootScope, $uibModal){
			var $ = jQuery;		
			
			$scope.litbomb = {};	
			
			$scope.dropBomb = function(){
				if(confirm('Are you sure you mean to drop this bomb?')){
					var request = {
						api: 'litBomb',
						date: $scope.litbomb.date,
						rkids : [],
						listRequest: $rootScope.appScope.listRequest
					}
					$.each($rootScope.appScope.knocklist.people, function(i, person){
						request.rkids.push(person.rkid);
					});
					$scope.appScope.runApi(request, function(revisedList){
						$rootScope.appScope.load_knocklist(revisedList);
						$scope.$close();
					}, 'json');					
				}
			}
			
			$scope.sendPostcards = function(){
				if(confirm('Are the postcards in the mail?')){
					var request = {
						api: 'send_postcards',
						listRequest: $rootScope.appScope.listRequest
					}
					$scope.appScope.runApi(request, function(revisedList){
						$rootScope.appScope.load_knocklist(revisedList);
						$scope.$close();
					}, 'json');					
				}
			
			}
			
		}
	]
);

app.controller('PersonAdderCtrl', 
	['$scope', '$rootScope', '$uibModal',
		function($scope, $rootScope, $uibModal){
			var $ = jQuery;		
			
			var st = $rootScope.appScope.query.stname;
			
			
			$scope.person = {
				stname : st,
				enroll: 'U',
				active: 1,
				city: 'Portland',
				state: 'ME'
			};	
			
			$scope.savePerson = function(){
				var request = {
					api: 'addPerson',
					person: $scope.person,
					listRequest: $rootScope.appScope.listRequest
				}
				$rootScope.appScope.runApi(request, function(revisedList){
					$rootScope.appScope.load_knocklist(revisedList);
					$scope.$close();
				});
			}

			
		}
	]
);

app.controller('FeaturePersonCtrl', 
	['$scope', '$rootScope', '$uibModal',
		function($scope, $rootScope, $uibModal){
			var $ = jQuery;			
			$scope.person = $rootScope.appScope.featured_person;
			$scope.newContact = {
				type : 'Post Card'
			};
			
			$scope.editBasicInfo = function(){
				$scope.$close();
				$rootScope.mode = 'Edit';
				$uibModal.open({
					templateUrl: rkvoters_config.template_dir + 'modal-personeditor.php',
					controller: 'FeaturePersonCtrl'
				});
			}
			
			$scope.goBack = function(){			
				$scope.$close();
				$uibModal.open({
					templateUrl: rkvoters_config.template_dir + 'modal-person.php',
					controller: 'FeaturePersonCtrl',
				});
			}
			
			// update person
			$scope.savePerson = function(mode){
				$scope.person.active = 1;
				var request = {
					api : 'updatePerson',
					rkid : $scope.person.rkid,
					person : $scope.person,
					listRequest: $rootScope.appScope.listRequest
				}
				$rootScope.appScope.runApi(request, function(person){
					$scope.person = $scope.appScope.updateList(person);

					if(mode == 1) $scope.$close();
					if(mode == 2) $scope.openNext();
				});
			}
			
			// record contact
			$scope.recordContact = function(progress){
				$scope.newContact.rkid = $scope.person.rkid;
				$scope.newContact.type = $rootScope.contactType;
				if($scope.newContact.type == 'Attended Event'){
					$scope.newContact.event_name = $rootScope.event_name;
				}
				if($scope.newContact.type == 'Phone Call'){
					$scope.newContact.status = $rootScope.callstatus;
				}

				$scope.newContact.support_level = $scope.person.support_level;
				
				var request = {
					api : 'recordContact',
					rkid : $scope.person.rkid,
					contact : $scope.newContact,
					person : $scope.person
				}
				$scope.appScope.runApi(request, function(person){
					$scope.newContact = { }; 
					$scope.person = $scope.appScope.updateList(person);
					if(progress) $scope.openNext();				
				}
				, 'json');
			}
			
			// open next person
			$scope.openNext = function(){
				$scope.$close();
								
				var i = $rootScope.appScope.selected_index;
				i++;
				if(i == $rootScope.appScope.knocklist.people.length){
					i = 0;
				}
				$rootScope.appScope.selected_index = i;
				var p = $rootScope.appScope.knocklist.people[i];
				$rootScope.appScope.openPerson(p);
			}
			
			// open prev person
			$scope.openPrev = function(){
				$scope.$close();				
			
				var i = $rootScope.appScope.selected_index;
				i--;
				if(i == -1){
					i = $rootScope.appScope.knocklist.people.length - 1;
				}
				$rootScope.appScope.selected_index = i;
				var p = $rootScope.appScope.knocklist.people[i];
				$rootScope.appScope.openPerson(p);					
			}
			
			$scope.openNeighbor = function(neighbor){
				$scope.$close();
				$rootScope.appScope.openPerson(neighbor);	
			}

			// remove
			$scope.removePerson = function(){
				if(confirm('Are you sure you want to remove this person?')){
					var request = {
						api : 'removePerson',
						rkid : $scope.person.rkid,
						listRequest: $rootScope.appScope.listRequest
					}
					$scope.appScope.runApi(request, function(response){
						if(response.status == 'deleted'){
							$scope.appScope.removePersonFromList();
							$scope.$close();
						}
					});
				}
			}
			
			// delete contact
			$scope.deleteContact = function(contact){
				var request = {
					api : 'deleteContact',
					vc_id : contact.vc_id,
					rkid: contact.rkid
				}
				$scope.appScope.runApi(request, function(person){
					$scope.newContact = {}; 
					$scope.person = person;
					$rootScope.appScope.load_person(person);
				}
				, 'json');
			}
		}
	]
);


app.controller('SearchFormCtrl', 
	['$scope', '$rootScope', '$uibModal',
		function($scope, $rootScope, $uibModal){
			var $ = jQuery;		

			$scope.counties = ['Androscoggin', 'Aroostook', 'Cumberland', 'Franklin', 'Hancock', 'Kennebec','Knox',  'Lincoln', 'Oxford', 
                            'Penobscot', 'Piscataquis', 'Sagadahoc', 'Somerset', 'Waldo', 'Washington', 'York'];

            $scope.regions = ['1. York County','2. Cumberland County','3. Coastal','4. Central','5. Western','6. Northern' ];
			
			$scope.query = $rootScope.query; 
	
			$scope.search = $rootScope.appScope.search;

			$scope.printList = $rootScope.appScope.printList;
	
			
		}
	]
);