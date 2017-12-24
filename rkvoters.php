<?php

/**
 * @package RK VOTERS
 */
/*
Plugin Name: RK VOTERS
Plugin URI: http://robkforcouncil.com/
Description: Super simple campaign management tool.
Version: 1.0.0
Author: Rob Korobkin
Author URI: http://robkorobkin.org
License: GPLv2 or later
Text Domain: crowdfolio
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

require_once("config.php");
require_once('rkvoters_model.php');


// LOAD THE APP
add_action( 'init', 'load_rkvoters' );
function load_rkvoters() {
	global $rkvoters_model;
	$rkvoters_model = new RKVoters_Model();
}



// RENDER THE TEMPLATE
function rkvoters_template_main(){

	// Grab the data model
	global $rkvoters_model;
	$rkvoters_model -> loadPlugin();
	load_rkvoters_client();

	// If you're not logged in, load the login screen
	if(!$rkvoters_model -> current_user -> isLoggedIn) {
		wp_enqueue_script('loggedout-js', plugins_url("client/loggedout/logged-out.js", __FILE__));
		include('client/loggedout/logged-out.php');
		return;
	}

	// If you are, load the main app interface
	include('client/templates/tpl-main.php');
}
add_shortcode( 'rkvoters', 'rkvoters_template_main' );




function load_rkvoters_client(){

	/* GOOGLE FONTS */
	$query_args = array(
		'family' => 'Open+Sans',
		'subset' => 'latin,latin-ext',
	);
	wp_enqueue_style(
		'rkvoters_google_fonts',
		add_query_arg( $query_args, "//fonts.googleapis.com/css" )
	);


	/* ENQUEUE FRAMEWORK */
	wp_enqueue_script("jquery");
	wp_enqueue_script('angularjs', "//ajax.googleapis.com/ajax/libs/angularjs/1.6.5/angular.min.js");
	wp_enqueue_script('angularbootstrap', plugins_url("client/third_party/ui-bootstrap-custom-tpls-2.5.0.min.js", __FILE__));
	wp_enqueue_style('angularbootstrapcss', plugins_url("client/third_party/ui-bootstrap-custom-2.5.0-csp.css", __FILE__));
	wp_enqueue_style('bootstrapcss', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');

	/* ENQUEUE APP */
	wp_enqueue_style( 'rkvoters_google_fonts' );
	wp_enqueue_style( 'rkvoters_css', plugins_url('client/rkvoters.css', __FILE__) );
	wp_enqueue_script('rkvoters_js', plugins_url('client/rkvoters.js', __FILE__));
}


// BOUNCE LOWER LEVEL USERS
add_action( 'wp_loaded', 'bounce_subscribers');
function bounce_subscribers() {
    if ( is_admin() ) { 

    	$user = wp_get_current_user();

		// Is the user an administrator?
		$user -> isAdmin = in_array( 'administrator', (array) $user->roles );

		if(!$user -> isAdmin) {
			header('Location: ' . site_url());
			exit;
		}
    }
}
