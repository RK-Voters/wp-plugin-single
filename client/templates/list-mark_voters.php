<div class="col-md-4 marker_sidebar" id="search_sidebar" ng-if="searchMode == 'query'">

<!-- 	<div class="tally">{{signed_tally}}</div> -->

	<input type="text" class="textInput" placeholder="First Name" ng-model="query.firstname" id="1_input" ng-focus="updateMarker($event)">
	<input type="text" class="textInput" placeholder="Last Name" ng-model="query.lastname" id="2_input" ng-focus="updateMarker($event)">
	<input type="text" class="textInput" placeholder="Num" ng-model="query.stnum" id="3_input" ng-focus="updateMarker($event)">
	<input type="text" class="textInput" placeholder="Street" ng-model="query.stname" id="4_input" ng-focus="updateMarker($event)">
	<input type="text" class="textInput" placeholder="City" ng-model="query.city" id="5_input" ng-focus="updateMarker($event)">
	<input type="text" class="textInput" placeholder="Zip" ng-model="query.zip" id="6_input" ng-focus="updateMarker($event)">

	<a ng-click="pickStreets()" style="float: left; display: block;margin-top: 20px">Pick Streets</a>
	
	<button class="rkbutton" ng-click="search()">SEARCH</button>


	<div class="street_list">{{ streets_obj.title_str }}</div>
</div>

<div class="col-md-4" ng-if="searchMode == 'streets'">
	<a ng-click="exitStreets()">&lt;&lt; BACK</a>
	<input ng-model="streets_obj.search_str" id="street_search" ng-keyup="filterStrings()" placeholder="Search..." />
	<p><b>{{ streets_obj.total}}:</b> {{ streets_obj.title_str }}</p>

	<div id="street_selector">
		<table>
			<tr ng-repeat="street in streets_obj.list" ng-if="!street.hide">
				<td><input type="checkbox" id="input_{{$index}}" ng-model="streets_obj.selected[$index]" ng-change="updateStreets()"/></td>
				<td><label for="input_{{$index}}">{{street.stname}} ({{street.total}})</label></td>
			</tr>
		</table>
	</div>

	<button class="rkbutton" ng-click="search()">SEARCH</button>
</div>


<div class="col-md-8">

	<div ng-repeat="address in knocklist.addresses" style="clear:both;" class="result clearfix">
		<div class="addressResult clearfix">
			<b ng-click="getAddress(address)">{{address.address}}</b>
			<div ng-repeat="person in address.residents" >


				<div class="person{{person.support_level}}" ng-class="{ signed : person.has_signed }">
					<span class="support_level{{person.support_level}}">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</span>
					&nbsp;<span class="addressResident" ng-click="openPerson(person)" id="person_{{person.rkid}}">{{person.residentLabel}}</span>
				</div>


			</div>
		</div>
	</div>
</div>