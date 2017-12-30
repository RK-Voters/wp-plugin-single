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