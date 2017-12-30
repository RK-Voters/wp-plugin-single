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