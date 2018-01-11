<div class="modalFrame clearfix personModal">

	<!-- TOP LEFT -->
	<div class="col-md-9" style="color: #666;">
		<b style="font-size: 18px; color: black;">{{person.firstname}} {{person.lastname}}</b>
		- <a ng-click="editBasicInfo()">EDIT</a>
		- <a ng-click="removePerson()">DELETE</a>
		<div>
			<span ng-if="person.phone">- {{person.phone}}</span>
			<span ng-if="person.phone2"> - {{person.phone2}}</span>
			<span ng-if="person.source == 'MailChimp' "> - From MailChimp</span>
		</div>
		<div ng-if="person.address != '0 undefined'">- {{person.address}}, {{person.city}} {{person.state}} {{person.zip}}</div>
		<div ng-if="person.age">- {{person.age}} - {{person.enroll}}</div>
		<div ng-if="person.employer">- {{person.employer}} - {{person.profession}}</div>
		<span ng-if="person.votedin2011 == 1"> - 2011</span>
		<span ng-if="person.votedin2013 == 1"> - 2013</span>			
	</div>

	<!-- TOP RIGHT -->
	<div class="col-md-3" style="text-align: right;">
		<div style="padding: 0 5px 10px;">
			<a ng-click="openPrev()">&lt;&lt;</a>
			&nbsp;&nbsp;&nbsp;
			<a ng-click="openNext()">&gt;&gt;</a>
		</div>
	</div>

	<!-- MIDDLE FIELDS -->
	<div class="topSection clearfix" style="clear:both;">
		<div class="col-md-4  field">
			<b>Support Level:</b>
			<br />
			<select ng-model="person.support_level">
				<option value="0">0 - Unidentified</option>
				<option value="1">1 - With Us</option>
				<option value="2">2 - Leaning Toward Us</option>
				<option value="3">3 - Undecided</option>
				<option value="4">4 - Leaning Against Us</option>
				<option value="5">5 - Against Us</option>					
			</select>			
		</div>
		<div class="col-md-4  field">
			<b>Email:</b>
			<br />
			<input placeholder="Email" ng-model="person.email" />
		</div>
		<div class="col-md-4  field">
			<b>Phone 
				<span ng-if="person.phoneType != ''">({{person.phoneType}})</span>
				<a ng-if="person.phone != ''" href="tel:{{person.phone}}">call</a>
				:
			</b>
			<br />
			<input placeholder="Phone" ng-model="person.phone" />
		</div>
		<div class="col-md-12 field">
			<b>Bio:</b>
			<br />
			<textarea ng-model="person.bio" style="height: 60px;"></textarea>
		</div>
		<div class="col-md-12">
			<label>
				<input id="volunteer_check" ng-model="person.volunteer" type="checkbox" style="width: 20px" 
						 ng-true-value="'true'" ng-false-value="''"/>&nbsp;
				Volunteer
			</label>
			&nbsp; &nbsp;
			<label>
				<input id="wants_sign_check" ng-model="person.wants_sign" type="checkbox" style="width: 20px" 
						 ng-true-value="'true'" ng-false-value="''"/>&nbsp;
				Wants Sign
			</label>
			&nbsp; &nbsp;
			<label>
				<input id="host_event_check" ng-model="person.host_event" type="checkbox" style="width: 20px" 
						 ng-true-value="'true'" ng-false-value="''"/>&nbsp;
				Host Event
			</label>
			&nbsp; &nbsp;
			<label>
				<input id="volunteer_other_check" ng-model="person.volunteer_other" type="checkbox" style="width: 20px" 
						 ng-true-value="'true'" ng-false-value="''"/>&nbsp;
				Other
			</label>

		</div>
		<div class="col-md-6">
			<div  ng-if="person.neighbors" class="neighbors">
				<br />
				<i>Folks at the same number \ address:</i>
				<ul>
					<li ng-repeat="neighbor in person.neighbors" ng-click="openNeighbor(neighbor)">
						{{neighbor.support_level}} - {{neighbor.residentLabel}} <i>{{neighbor.bio}}</i>
					</li>
				</ul>
			</div>
		</div>
		<div class="col-md-6">
			<button ng-click="savePerson(2)" class="rkbutton" >Save & Next</button>
			<button ng-click="savePerson(1)"  class="rkbutton" style="margin-right: 10px">Save & Close</button>
		</div>
	</div>

	<!-- BOTTOM SECTION - CONTACT MANAGER -->
	<div class="bottomSection">
		<div class="col-md-6">
			<!-- <div>
				<select ng-model="person.closed">
					<option value="0">Person is Open</option>
					<option value="1">Person is Closed</option>						
				</select>
				<br /><br />
			</div>
		
			<i>Add Contact</i> -->
			<select ng-model="$root.contactType">
				<option>Attended Event</option>
				<option>Chat at Door</option>
				<option>Chat on Street</option>
				<option>Chat Elsewhere</option>
				<option>Donation</option>					
				<option>Email</option>
				<option>Lit Drop</option>
				<option>Phone Call</option>
				<option>Post Card</option>
				<option>Sent Post Card</option>	
				<option>Update</option>				
			</select>
			
			<br /><br />

			<input 	type="text" class="form-control"
					is-open="focus" ng-focus="focus=true"
					uib-datepicker-popup="shortDate"
					ng-model="newContact.datetime"
					close-text="Close"
					placeholder="Enter date..." />


			<div ng-if="$root.contactType == 'Donation'">
				<br />
				<b>Amount:</b>
				<br /><input type="number" ng-model="newContact.amount" />
			</div>

			<div ng-if="$root.contactType == 'Attended Event'" style="margin: 10px 0">
				<input ng-model="$root.event_name" placeholder="Event Name..." />
			</div>

			<br />
			<select ng-if="$root.contactType == 'Phone Call'" ng-model="$root.callstatus" 
					style="margin: 0 0 10px">
				<option>Connection</option>
				<option>VM - Person Confirmed - Message</option>
				<option>VM - Not Confirmed - Message</option>					
				<option>VM - Person Confirmed - No Message</option>
				<option>VM - Not Confirmed - No Message</option>
				<option>Just Ringing</option>
				<option>Bad Number</option>
			</select>
			<br />
			<textarea ng-model="newContact.note" placeholder="Note..."></textarea>
										
			<br />
			<button  class="rkbutton" ng-click="recordContact(1)">Post & Next</button>
			<button  class="rkbutton" ng-click="recordContact()" style="margin-right: 10px">Post</button>
			
			
		</div>


		<div class="col-md-6" style="border-left: dashed 1px #ccc;">
			<i>Log</i>
			<div ng-repeat="contact in person.contacts">
				<i>{{contact.datetime.split(' ')[0]}}</i> - 
				<a ng-click="deleteContact(contact)">X</a>
				<br /><b>{{contact.type}}</b> 
				<span ng-if="contact.support_level != ''">
					 - {{contact.support_level}}
				</span>
				<span ng-if="contact.event_name != ''">
					 - {{contact.event_name}}
				</span>
				<span ng-if="contact.status != ''">
					 - {{contact.status}}
				</span>
				<span ng-if="contact.note != ''">
					 - {{contact.note}}
				</span>
				<span ng-if="contact.amount != 0">
					- ${{contact.amount}}
				</span>
				<span ng-if="contact.userName != 0">
					<i>- {{contact.user_name}}</i>
				</span>
				<br /><br />
			</div>
		</div>
	
	</div>
	
</div>