

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
			<button class="rkbutton print_button"  ng-click="markVoters()">MARK SIGNERS</button>

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
	
		<?php
			$views = array("individuals", "absentees", "addresses", "knocknotes", "multisheet", "mark_voters");
			foreach($views as $v){
				echo '<div ng-if="$root.viewMode == \'' . $v . '\'" class="results clearfix">';
				include("list-$v.php");
				echo '</div>';
			}
		?>
			
	</div>


	<!-- REPORT MANAGER -->
	<div ng-if="component=='report'" class="clearfix report_frame" style="clear: both;">

		<!-- TOP LINE -->
		<div class="row" style="margin: 15px 0 30px;">
			<div class="col-md-6"> 
				<div style="float: left;">
					<select ng-model="viewMode" ng-change="updateReportMode(viewMode)" style="width: 300px;">
						<option value="fundraising">FUNDRAISING REPORT</option>
						<option value="mailchimp">MAILING LIST</option>
					</select>
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
				<a ng-click="downloadDonors()">Download</a>
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
	</div>
	
</div>

<form id="hiddenForm" target="_blank" method="post" action="" style="display: none"></form>
<form id="hiddenForm2" target="_blank" method="post" action="" style="display: none"></form>


