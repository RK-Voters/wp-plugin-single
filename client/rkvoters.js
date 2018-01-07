

var app = angular.module('RKVApp', ['ui.bootstrap']);

app.controller('RKVCtrl', ['$scope', '$http', '$sce', '$rootScope', '$window', '$uibModal',
	function($scope, $http, $sce, $rootScope, $window, $uibModal){
		
		// INIT STATE
		var $ = jQuery;
		$scope.init = function(){			
			$rootScope.appScope = $scope;
			$scope.people = {};
		
			$rootScope.query = {
				firstname : "",
				lastname : "",
				support_level : "Support Level",
				enroll : "Party",
				sex : "Gender",
				age_range: "Age",
				
				active: true,
				has_phone: true,
				never_called: true,

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
									' - ' + person.enroll + ' - ' + person.age;
			
			person.residentLabel = person.residentLabel.toUpperCase();
			
			if(person.votedin2011 == 1) {
				
				person.residentLabel += '*';
			}
			if(person.votedin2013 == 1) person.residentLabel += '*';				
			
			if(!('active' in person)) person.active = true;
			
			var cb_fields = ["volunteer", "wants_sign", "host_event", "volunteer_other"];
			$.each(cb_fields, function(i, f){ 
				person[f] = (person[f] == "1") ? "true" : '';
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
			});
		}

		
		
		// UI CONTROLS
		$scope.loadComponent = function(componentName){
			$scope.component = componentName;
			if(componentName == 'report'){
				$scope.showMap = 1;
				$rootScope.viewMode = 'totals';
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


		// API
		$scope.runApi = function(request, callback){
			request.access_token = rkvoters_config.access_token;
			request.campaign_slug = rkvoters_config.campaign_slug;
			request.user_name = rkvoters_config.user_name;
			$http({
				method: 'POST',
				url: rkvoters_config.api_url,
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
	
			
		}
	]
);