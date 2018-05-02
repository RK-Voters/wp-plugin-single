<?php

	global $voter_list, $query, $list_size;

	$mapURL = 	'https://www.google.com/maps/embed/v1/place?key=AIzaSyC6MLx8c1eQORx3uTNmL5RwXY761YSXaVs&zoom=13' .
				'&q=' . urlencode($query -> stname) . '+' . urlencode($query -> city) . '+ME';


?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Print - Knock List</title>


		<link rel="stylesheet" href="<?php echo site_url(); ?>/wp-content/themes/rkvoters_theme/style.css" />
		<link rel="stylesheet" href="<?php echo site_url(); ?>/wp-content/themes/dion/style.css" />
		
		<link rel='stylesheet' id='rkvoters_css-css'  href='<?php echo site_url(); ?>/wp-content/plugins/rkvoters/client/rkvoters.css?ver=4.9.2' type='text/css' media='all' />



		<link rel='stylesheet' id='angularbootstrapcss-css'  href='http://localhost/biz/_rkvoter/site/dion/wp-content/plugins/rkvoters/client/third_party/ui-bootstrap-custom-2.5.0-csp.css?ver=4.9.2' type='text/css' media='all' />
		<link rel='stylesheet' id='rkvoters_google_fonts-css'  href='//fonts.googleapis.com/css?family=Open+Sans&#038;subset=latin%2Clatin-ext&#038;ver=4.9.2' type='text/css' media='all' />
		<link rel='stylesheet' id='bootstrapcss-css'  href='//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css?ver=4.9.2' type='text/css' media='all' />


		<style>
			body { background: white !important; margin: 20px;}
			.field { padding-top: 15px; }
			.sub { font-size: 11px; }
				
		</style>


	</head>
	<body>

		<h1><?php echo $query -> city; ?></h1>

		<h4><?php echo $query -> stname; ?></h4>

		<h4><?php echo count($voter_list) . ' Addresses. ' . $list_size . ' Voters.'; ?></h4>		

		<iframe
		  width="500px"
		  height="500px"
		  frameborder="0" style="border:0"
		  src="<?php echo $mapURL; ?>" allowfullscreen>
		</iframe>


		<div style="padding: 0px 0 30px; font-size: 11px">
			
			<div class="field">________ Contacts</div>
			<div class="field">________ Supporters</div>
			<div class="field">________ Sign Requests</div>
			<div class="field">________ Volunteers</div>
		
			<div class="field"><br />________________________________________________________
				<div class="sub">CANVASSER</div>
			</div>

			<div class="field"><br/>______________________________________
				<div class="sub">DATE</div>
			</div>
			
		</div>



		<?php 
			$current_street = ''; $voter_list[0] -> stname;

			foreach($voter_list as $address){

				if($current_street != $address -> stname) {
					$current_street = $address -> stname;
					echo '<b>' . $current_street . "</b>";
				}

				echo 	'<div style="clear:both;" class="result clearfix">
							<div style="font-style: italic;">' . $address -> address . '</div>
							<div class="addressResult col-sm-7 clearfix">';

								foreach($address -> residents as $person){

									echo '	<div>
												<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
												<span class="addressResident">' . $person -> residentLabel . '</span>';
												if($person -> phone != '') echo '<span style="font-weight: bold"> - ' . $person -> phone . '</span>';
									echo	'</div>';
								}

				echo 	 	'</div>
							<div class="col-sm-5" style="font-size: 11px">';

								foreach($address -> residents as $person){
									if($person -> support_level != 0){

										echo '	<b style="text-transform: uppercase;">' . $person -> firstname . ' ' . $person -> lastname . '</b>';
										if($person -> phone != '') echo '<span> - ' . $person -> phone . '</span>';
										echo '<br />Support Level: ' . $person -> support_level . '. <span ng-if="person.has_signed"> Signed Petition. </span>' . 
											$person -> bio . '<br /><br />';
									}
								}
				echo   		'</div>
						</div>';
			}
		?>
		
	</body>
</html>