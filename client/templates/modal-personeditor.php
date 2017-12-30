<div class="modalFrame clearfix">
	<div><b style="font-size: 18px">{{$root.mode}} Person 
		<span ng-if="$root.mode == 'Edit'">- <a ng-click="goBack()">Back</a></span>:
	</div>

	<div class="col-md-6">
		<label>First Name</label>
		<input ng-model="person.firstname" />
	</div>
	<div class="col-md-6">
		<label>Last Name</label>
		<input ng-model="person.lastname" />
	</div>
	<div class="col-md-12">
		<label>Bio</label>
		<textarea ng-model="person.bio"></textarea>
	</div>
	
	<div class="col-md-6">
		<label>Street Number</label>
		<input ng-model="person.stnum" />
		<br />
		<label>Street Name</label>
		<input ng-model="person.stname" />
		<br />
		<label>Unit</label>
		<input ng-model="person.unit" />
		<br />
		<label>City</label>
		<input ng-model="person.city" />
		<br />
		<label>State</label>
		<input ng-model="person.state" />
		<br />
		<label>Zip</label>
		<input ng-model="person.zip" />
	</div>
	<div class="col-md-6">
		<label>Support Level</label>
		<select ng-model="person.support_level">
			<option value="0">0 - Unidentified</option>
			<option value="1">1 - With Us</option>
			<option value="2">2 - Undecided</option>
			<option value="3">3 - Against Us</option>
		</select>		
		<br />
		<label>Party</label>
		<select ng-model="person.enroll">
			<option value="U">Un-enrolled \ Unknown</option>
			<option value="G">Green</option>
			<option value="D">Democrat</option>
			<option value="R">Republican</option>
		</select>		
		<br />
		<label>Year of Birth</label>
		<input ng-model="person.yob" />
		<br />
		<label>Email</label>
		<input ng-model="person.email" />
		<br />
		<label>Phone - Work</label>
		<input ng-model="person.phone" />
		<br />
		<label>Phone - Mobile</label>
		<input ng-model="person.phone2" />
		<br />
		<label>Profession</label>
		<input ng-model="person.profession" />
		<br />
		<label>Employer</label>
		<input ng-model="person.employer" />

		<br /><br />
		<button ng-click="savePerson()" class="btn btn-primary">SAVE!</button>
		<button ng-click="savePerson(1)" class="btn btn-primary">SAVE AND CLOSE!</button>			
	</div>
	
	
	
</div>
