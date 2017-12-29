<div class="modalFrame clearfix">

	<div class="col-md-6">
		<b>View Mode?</b>
		<br />
		<select ng-model="$root.viewMode">
			<option>addresses</option>
			<option>individuals</option>
			<option>knocknotes</option>
			<option>mark absentees</option>
			<option>multi-sheet</option>								
		</select>
	</div>
	


	<div class="col-md-6">
		<b>Drop a Lit Bomb on this street?</b>
		<br />
		<input 
			type="text" class="form-control" 
			datepicker-popup="shortDate" 
			ng-model="litbomb.date" 
			close-text="Close" 
			placeholder="Enter date..." />
		<br />
		<button ng-click="dropBomb()">DROP!</button>
		
		<br /><br /><br />
		<b>Send Post Cards:</b>
		<br /><button ng-click="sendPostcards()">SEND!</button>
	</div>
	
</div>