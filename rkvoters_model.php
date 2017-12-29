<?php

	Class RKVoters_Model {

		function __construct(){
			session_start();

			// load the wordpress database handler
			global $wpdb;
			$this -> wpdb = $wpdb;


			// load the request
			if(count($_POST) == 0){
				$this -> request = (array) json_decode(file_get_contents('php://input'));
			}
			else {
				$this -> request = $_POST;
			}

		}

		// load the plugin's assets when you're about to render it
		function loadPlugin() {

			// load the current campaign
			$this -> current_campaign = $this -> _get_current_campaign();


			// load the current user
			$this -> current_user = $this -> _get_current_user();

		}

		function outputError(){
			// Print last SQL query string
			echo $this -> wpdb->last_query;

			// Print last SQL query result
			echo $this -> wpdb->last_result;

			// Print last SQL query Error
			echo $this -> wpdb->last_error;

		}

		// GET FULL CAMPAIGN
		function _get_current_campaign(){

			if(isset($this -> current_campaign)){
				return $this -> current_campaign;
			}

			$current_campaign = get_blog_details();

			// get slug
			if(SUBDOMAIN_INSTALL) {
					$slug = explode('//', explode('.', $current_campaign -> siteurl)[0])[1];
			}
			else {
				$slug = end(explode('/', $current_campaign -> siteurl));
			}
			$current_campaign -> slug = $slug;

			// get img
			$current_campaign -> img = get_stylesheet_directory_uri() . "/" . $slug . ".jpg";

			return $current_campaign;

		}


		// GET USER
		function _get_current_user(){
			$user = wp_get_current_user();

			// this should probably be something else (currently: is administrator?)
			$user -> isLoggedIn = in_array( 'delete_others_posts', (array) $user->allcaps );

			$user -> rkvoters_access_token = get_field('rkvoters_access_token', 'user_' . $user -> ID);

			return $user -> data;

		}


		// GET CLIENT-SIDE DATA
		function get_clientdata(){
			global $rkvoters_config;

			$clientdata = array(
				"api_url" => $rkvoters_config['api_url'],
				"template_dir" => plugins_url() . "/rkvoters/client/templates/",
				"user_wpid" => $this -> current_user -> ID,
				"user_name" => $this -> current_user -> user_login,
				"access_token" => $this -> current_user -> rkvoters_access_token,
				"campaign_slug" => $this -> current_campaign -> slug
			);

			return $clientdata;
		}


	}
