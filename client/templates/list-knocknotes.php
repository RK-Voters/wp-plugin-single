<div ng-repeat="address in knocklist.addresses" style="clear:both;" class="result clearfix">
	<div class="addressResult col-sm-4 clearfix">
		<b ng-click="getAddress(address)">{{address.address}}</b>
		<div ng-repeat="person in address.residents" >
			<div class="person{{person.support_level}}">
				<span class="support_level{{person.support_level}}">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</span>
				&nbsp;<span class="addressResident" ng-click="openPerson(person)">{{person.residentLabel}}</span>
				<span ng-if="person.statefileid == ''">- <a ng-click="startMerge(person)" id="merge_{{person.rkid}}" class="merge_label">MERGE</a></span>
			</div>
		</div>
	</div>

	<div class="col-sm-8" style="font-size: 11px">
		<div ng-repeat="person in address.residents" ng-if="person.support_level != 0">
			<b style="text-transform: uppercase;">{{person.firstname}} {{person.lastname}}</b>
			<span ng-if="person.phone != ''"> - {{person.phone}}</span>
			<br />Support Level: {{person.support_level}}<span ng-if="person.has_signed"> - Signed Petition</span><span ng-if="person.bio"> - {{person.bio}}</span>
			<br /><br />
		</div>
	</div>
	<div class="clearfix" ng-if="$index % 3 == 2"></div>
</div>