<?php
	global $rkvoters_model;

?>


<div class="loggedOut">
	<h2 style="font-size: 30px; margin-top: 0; text-align: center">Log In</h2>
	<div	class="logged_out_img">
		<img src="<?php echo $rkvoters_model -> current_campaign -> img; ?>" />
	</div>
	<?php

		if(isset($_GET['login']) && $_GET['login'] == 'failed'){
			echo '<p style="color: red;">Wrong user name or password.</p>';
		}

		wp_login_form(
			array(
					"redirect" =>	site_url( '' )
			)
		);
	?>
	<a href="<?php echo site_url() . "/wp-login.php?action=lostpassword&redirect_to=" . get_permalink(); ?>" title="Lost Password">Lost Password</a>
</div>
