

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
			<button class="rkbutton" ng-click="openSearchForm()">SEARCH</button>
			
			<button class="rkbutton"  ng-click="openPersonAdder()">ADD PERSON</button>

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
			<?php
				$views = array("individuals", "absentees", "addresses", "knocknotes", "multisheet");
				foreach($views as $v){
					echo '<div ng-if="$root.viewMode == \'' . $v . '\'">';
					include("list-$v.php");
					echo '</div>';
				}
			?>
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
					<button  class="rkbutton" ng-click="updateTotals()">UPDATE TOTALS</button>
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


