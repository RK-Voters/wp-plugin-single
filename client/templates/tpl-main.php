

<script>
	// load server-side data
	<?php
		global $rkvoters_model;
		echo 'var rkvoters_config = ' . json_encode($rkvoters_model -> get_clientdata());

	?>;
	console.log(rkvoters_config);
</script>


<div class="innerFrame" ng-app="RKVApp" ng-controller="RKVCtrl">

	<div class="topLine">

		<div class="interface_controller">
			<a ng-click="loadComponent('report')" ng-if="component=='knocklist'">&lt; &lt; See Report</a>
			<a ng-click="loadComponent('knocklist')"  ng-if="component=='report'">
				&lt; &lt; See Knocklists
			</a>
		</div>
		<div class="interface_btns clearfix">
			<button ng-click="openSearchForm()">SEARCH</button>
			
			<button ng-click="openPersonAdder()">ADD PERSON</button>

		</div>
	</div>


	<div ng-if="component=='knocklist'">
		<div class="searchFilter">
				
			<div style="float: right; padding-top: 4px">
				<a ng-click="openListManager()">{{contactList.length_label}}</a>
			</div>
		</div>
	
		<div class="mapFrame" ng-if="showMap == 1">	
			<iframe
			  width="100%"
			  height="300"
			  frameborder="0" style="border:0"
			  ng-src="{{map.safeUrl}}" allowfullscreen>
			</iframe>
		</div>
	
		<div class="results clearfix">
			<div ng-if="$root.viewMode == 'individuals'">	
				<div class="col-sm-8" style="font-size: 11px">
					<div ng-repeat="person in knocklist.people">
						<span class="support_level{{person.support_level}}">
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</span>&nbsp;
						<span ng-click="openPerson(person, $index)" style="font-weight: bold; text-transform: uppercase;">{{person.firstname}} {{person.lastname}}</span>
						<span ng-if="!person.firstname" ng-click="openPerson(person, $index)" style="font-weight: bold; text-transform: uppercase;">{{person.email}} </span>

						<br />{{person.profession}} - {{person.employer}}
						<br />
						<span ng-if="person.phone != ''"> - {{person.phone}}</span>
						<span ng-if="person.phone2 != ''"> - {{person.phone2}}</span>
						<div ng-if="person.bio">{{person.bio}}</div>
						
						<br /><br />
					</div>
				</div>
			</div>
		
			<div ng-if="$root.viewMode == 'mark absentees'"> 
				<div ng-repeat="address in knocklist.addresses">	
					<div class="result addressResult col-sm-4 clearfix">
						<b ng-click="getAddress(address)">{{address.address}}</b>
						<div ng-repeat="person in address.residents" 
							ng-if="person.active">
							<div class="person{{person.support_level}}">
								<span class="support_level{{person.support_level}}">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</span>
								&nbsp;
								<span ng-click="openPerson(person, $index)">{{person.residentLabel}}</span>
								- <span ng-click="setsupport_level(4, person)" class="addressResident" >P</span>
								- <span ng-click="setsupport_level(5, person)" class="addressResident" >V</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		
			<div ng-if="$root.viewMode == 'addresses'"> 
				<div ng-repeat="address in knocklist.addresses">
			
					<div class="result addressResult col-sm-4 clearfix">
						<b ng-click="getAddress(address)">{{address.address}}</b>
						<div ng-repeat="person in address.residents" 
							class="addressResident" ng-if="person.active">
							<div ng-click="openPerson(person)" class="person{{person.support_level}}">
								<span class="support_level{{person.support_level}}">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</span>
								&nbsp;{{person.residentLabel}}
							</div>
						</div>
					</div>

					<div class="clearfix" ng-if="$index % 3 == 2"></div>

				</div>

				<div ng-repeat="person in knocklist.contacts" style="clear: both; font-size: 11px">
					<br /><br />
					<b style="text-transform: uppercase;" ng-click="openPerson(person, $index)">{{person.firstname}} {{person.lastname}}</b> - {{person.enroll}} - {{person.age}}
					
					<br />support_level: {{person.support_level}}
					<br />{{person.bio}}
				</div>
			</div>
		</div>
		
		<div ng-if="$root.viewMode == 'knocknotes'"> 
			<div ng-repeat="address in knocklist.addresses" style="clear:both;" class="result clearfix">
				<div class="addressResult col-sm-4 clearfix">
					<b ng-click="getAddress(address)">{{address.address}}</b>
					<div ng-repeat="person in address.residents" 
						class="addressResident" ng-if="person.active">
						<div ng-click="openPerson(person)" class="person{{person.support_level}}">
							<span class="support_level{{person.support_level}}">
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							</span>
							&nbsp;{{person.residentLabel}}
						</div>
					</div>
				</div>

				<div class="col-sm-8" style="font-size: 11px">
					<div ng-repeat="person in address.residents" ng-if="person.support_level != 0">
						<b style="text-transform: uppercase;">{{person.firstname}} {{person.lastname}}</b>
						<span ng-if="person.phone != ''"> - {{person.phone}}</span>
						<br />support_level: {{person.support_level}} - {{person.bio}}
						<br /><br />
					</div>
				</div>
				<div class="clearfix" ng-if="$index % 3 == 2"></div>
			</div>
		</div>
		
		<div ng-if="$root.viewMode == 'multi-sheet'"> 
			<div ng-repeat="street in street_sets" style="clear:both; page-break-inside: auto" class="result clearfix">
			
				<h3>{{street.street_name}}</h3>
				<iframe
				  width="100%"
				  height="300"
				  frameborder="0" style="border:0"
				  ng-src="{{street.safeUrl}}" allowfullscreen>
				</iframe>
			
				<div ng-repeat="address in street.addresses" style="clear:both;" class="result clearfix">
					<div class="addressResult col-sm-5 clearfix">
						<b ng-click="getAddress(address)">{{address.address}}</b>
						<div ng-repeat="person in address.residents" 
							class="addressResident" ng-if="person.active">
							<div ng-click="openPerson(person)" class="person{{person.support_level}}">
								<span class="support_level{{person.support_level}}">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</span>
								&nbsp;{{person.residentLabel}}
							</div>
						</div>
					</div>

					<div class="col-sm-7" style="font-size: 11px">
						<div ng-repeat="person in address.residents" ng-if="person.support_level != 0">
							<b style="text-transform: uppercase;">{{person.firstname}} {{person.lastname}}</b>
							<br />support_level: {{person.support_level}} - {{person.bio}}
							<br /><br />
						</div>
					</div>
					<div class="clearfix" ng-if="$index % 3 == 2"></div>
				</div>
				
				<div style="page-break-after: always;">&nbsp;&nbsp;</div>
			</div>
		</div>
			
	</div>


	<!-- REPORT MANAGER -->
	<div ng-if="component=='report'" class="clearfix report_frame" style="clear: both;">

		<!-- TOP LINE -->
		<div class="row" style="margin: 15px 0 30px;">
			<div class="col-md-6"> 
				<div style="float: left;">
					<select ng-model="viewMode" ng-change="updateReportMode(viewMode)">
						<option value="totals">VIEW TOTALS</option>
						<option value="assigner">REDRAW DISTRICTS</option>
						<option value="fundraising">FUNDRAISING REPORT</option>
						<option value="emailLocalSupporters">EMAIL LOCAL SUPPORTERS</option>
						<option value="mailingList">MAILING LIST</option>
					</select>
					<button ng-click="updateTotals()">UPDATE TOTALS</button>
				</div>
				<form action="" target="_blank" style="float: left; margin-left: 5px;">
					<input type="hidden" name="export" value="emails" />
					<input type="submit" value="EXPORT EMAILS"  />
				</form>
			</div>
			<form class="col-md-6" style="text-align: right;" >
				{{total_counts.active_voters|number}} Voters |
				{{total_counts.contacts|number}} Contacts | 
				{{total_counts.likes|number}} Likes
				({{ (100 * total_counts.likes / total_counts.contacts) |number}}%)
			</form>
		</div>

		<!-- TURF ASSIGNER -->
		<div class="turf_assigner col-md-9" ng-if="viewMode == 'assigner'">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-9">
					<div ng-repeat="turf in turf_hash" class="col-md-2" style="padding: 0 3px">
						<i style="font-size: 11px;">{{turf.turf_name}}</i>
					</div>
				</div>
			</div>
			
			<div ng-repeat="turfObj in streets" style="margin-bottom: 15px;">
				<div>
					<b>{{turfObj.turf.turf_name}}</b> 
					- <a ng-click="toggleStreetSet(turfObj)">{{turfObj.toggle_command}}</a>
				</div>
				<div style="max-height: 400px; overflow-y: scroll; overflow-x: hidden; 
						border: solid 1px #ccc; margin: 5px 0; padding: 5px;" ng-if="turfObj.state == 'open'">
					<div class="row" ng-repeat="street in turfObj.streets" 
							style="padding: 10px 0; border-bottom: solid 1px #ccc">
						<div class="col-md-3">
							<a ng-click="updateMap(street.street_name)">
								{{street.street_name}}
							</a>
						</div>
						<div class="col-md-9">
							<div ng-repeat="turf in turf_hash" class="col-md-2">
								<input 	type="radio" name="street_{{street.streetid}}"
										ng-model="street.turfid"
										value="{{turf.turfid}}"
										ng-checked="street.turfid == turf.turfid"
										ng-change="updateTurfAssignment(street, turfObj.turf.turfid)" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- TOTALS -->
		<div class="turf_totals col-md-9" ng-if="viewMode == 'totals'">		
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-9">
					<div ng-repeat="total in totals" class="col-md-4" 
						style="padding: 0 3px 5px; text-align: center;">
						<i style="font-size: 13px; text-transform: uppercase; font-weight: bold">{{total}}</i>
					</div>
				</div>
			</div>
			
			<div ng-repeat="turfObj in streets" style="margin-bottom: 15px;">
				<div class="row">
					<div class="col-md-3">
						<b>{{turfObj.turf.turf_name}}</b> 
						- <a ng-click="toggleStreetSet(turfObj)">{{turfObj.toggle_command}}</a>
					</div>
					<div class="col-md-9" style="text-align: center;">
						<div ng-repeat="total in turfObj.turf.totals" class="col-md-4">
							<div class="col-md-6">{{total.num | number}}</div>
							<div class="col-md-6">{{total.percent}}</div>
						</div>
					</div>
				</div>
				<div style="max-height: 400px; overflow-y: scroll; overflow-x: hidden; 
							border: solid 1px #ccc; margin: 5px 0; padding: 5px; background: #f5f5f5" 
					ng-if="turfObj.state == 'open'">
	
					<div class="row" ng-repeat="street in turfObj.streets" 
							style="padding: 10px 0; border-bottom: solid 1px #ccc">
						<div class="col-md-3">
							<a ng-click="openStreet(street.street_name)">
								{{street.street_name}}
							</a>
							- <a ng-click="updateMap(street.street_name)">MAP</a>
						</div>
						<div class="col-md-9" style="text-align: center;">
							<div ng-repeat="total in street.totals" class="col-md-4">
								<div class="col-md-6">{{total.num}}</div>
								<div class="col-md-6">{{total.percent}}</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- FUNDRAISING -->
		<div class="turf_totals col-md-9" ng-if="viewMode == 'fundraising'">
			
			<div style="font-size: 16px; margin-bottom: 40px;">
				<span style="font-weight: bold;">TOTAL: </span>
				<span>{{donationTotal | currency}}</span>				
			</div>

			<div style="font-size: 16px; margin-bottom: 40px;">
				<a href="?export=donors" target="_blank">Download</a>
			</div>
				
				
			<div ng-repeat="donationSet in donationSets">
				<div style="font-weight: bold; font-size: 16px;">{{donationSet.title}}</div>
				<div class="row clearfix" 
					 ng-repeat="donation in donationSet.donors" style="margin: 3px 0;">
					<div style="width: 80px; float: left; ">
						{{donation.amount | currency}}
					</div>
					<div style="width: 80px; float: left; ">
						{{ donation.datetime }}
					</div>
					<div style="float: left;">
						<a ng-click="openPerson(donation)">
							{{donation.firstname}} {{donation.lastname}}
						</a>
					</div>
				</div>
				
				<div class="row" >
					<div class="col-md-6" style="border-top: solid 1px #ccc;">
						<i>{{donationSet.total | currency}}</i>
					</div>
				</div>
				<br /><br />
			</div>			
		</div>

		<!-- GENERATE EMAIL LIST -->
		<div class="turf_totals col-md-9" ng-if="viewMode == 'emailLocalSupporters'">
			{{localSupporterEmailList}}
		</div>

		<!-- MAILING LIST -->
		<div class="turf_totals col-md-9" ng-if="viewMode == 'mailingList'">
			{{listLength | number}} Records<br /><br />
			<a href="?export=mailinglist">DOWNLOAD</a><br /><br />
			<div ng-repeat="address in mailingList">
				<b>{{address.name}}</b> <br />{{address.address}}<br /><br />
			</div>
		</div>		
		
		<div class="map_frame col-md-3">
			<iframe
			  width="100%"
			  height="600"
			  frameborder="0" style="border:0"
			  ng-src="{{map.safeUrl}}" allowfullscreen>
			</iframe>
		</div>
	</div>
	
</div>


