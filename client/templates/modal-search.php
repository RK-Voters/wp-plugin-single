<div class="search_container">
	<div id="search_form">
		<div class="header">Search:</div>
		<div class="rk_row">
			<input type="text" class="textInput" placeholder="First Name" ng-model="query.firstname">
			<input type="text" class="textInput" placeholder="Last Name" ng-model="query.lastname">
			<select ng-model="query.support_level">
				<option>Support Level</option>
				<option value="1">0 - Unidentified</option>
				<option value="1">1 - With Us Strongly</option>
				<option value="2">2 - Leaning Our Way</option>
				<option value="3">3 - Undecided</option>
				<option value="4">4 - Leaning Away</option>
				<option value="5">5 - Opposed</option>
			</select>
		</div>
		<div class="rk_row">
			<select ng-model="query.enroll">
				<option>Party</option>
				<option>D</option>
				<option>R</option>
				<option>U</option>
			</select>
			<select ng-model="query.sex">
				<option>Gender</option>
				<option>F</option>
				<option>M</option>
			</select>
			<select ng-model="query.age_range">
				<option>Age</option>
				<option>18-35</option>
				<option>35-50</option>
				<option>50-65</option>
				<option>65-80</option>
				<option>80+</option>
			</select>
		</div>
		<div class="rk_row">
			<table>
				<tr>
					<td><input type="checkbox" class="checkbox" id="vol_ctrl" ng-model="query.volunteer" /></td>
					<td><label for="vol_ctrl">Volunteer</label></td>
					<td><input type="checkbox" id="sign_ctrl" class="checkbox" ng-model="query.wants_sign" /></td>
					<td><label for="sign_ctrl">Lawn Sign</label></td>
					<td><input type="checkbox" class="checkbox" id="host_ctrl" ng-model="query.host_event" /></td>
					<td><label for="host_ctrl">Hosting Event</label></td>

				</tr>
				<tr>
					<td><input type="checkbox" class="checkbox" id="phone_ctrl" ng-model="query.has_phone" /></td>
					<td><label for="phone_ctrl">Has Phone</label></td>
					<td><input type="checkbox" id="never_ctrl" class="checkbox" ng-model="query.never_called" /></td>
					<td><label for="never_ctrl">Never Called</label></td>
					<td><input type="checkbox" id="only_ctrl" class="checkbox" ng-model="query.only_active" /></td>
					<td><label for="only_ctrl">Active Voters</label></td>
				</tr>
			</table>
		</div>
		<div class="rk_row">
			<div class="subhead">Address</div>
		</div>			
		<div class="rk_row">
			<input type="text" class="textInput" placeholder="Num" ng-model="query.stnum">
			<input type="text" class="textInput" placeholder="Street" ng-model="query.stname">
			<input type="text" class="textInput" placeholder="City" ng-model="query.city">
		</div>
		<div class="rk_row">
			<select ng-model="query.county">
				<option>County</option>
				<option ng-repeat="county in counties">{{county}}</option>
			</select>
			<select ng-model="query.region">
				<option>Region</option>
				<option ng-repeat="region in regions">{{region}}</option>
			</select>
			<select ng-model="query.turf">
				<option>Turf</option>
			</select>
		</div>
		<button ng-click="search()">SEARCH</button>
		<div style="clear: both;"></div>
	</div>
</div>