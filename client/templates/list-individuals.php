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