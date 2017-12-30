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