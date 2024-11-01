<?php
/*
Plugin Name: Tweet Manager
Plugin URI: http://MyWebsiteAdvisor.com
Description: Tweet Manager plugin creates an admin page that allows you to manually post your pages/posts/CPTs to your twitter via API
Version: 1.1.5
Author: MyWebsiteAdvisor
Author URI: http://MyWebsiteAdvisor.com
*/

register_activation_hook(__FILE__, 'tweeter_activate');




function tweeter_activate() {
	
	// display error message to users
	if ($_GET['action'] == 'error_scrape') {                                                                                                   
		die("Sorry, Plugin requires PHP 5.0 or higher. Please deactivate plugin.");                                 
	}

	if ( version_compare( phpversion(), '5.0', '<' ) ) {
		trigger_error('', E_USER_ERROR);
	}

}


// require Transparent Watermark Plugin if PHP 5 installed
if ( version_compare( phpversion(), '5.0', '>=') ) {
	define('TWEETER_LOADER', __FILE__);

	require_once(dirname(__FILE__) . '/plugin-template.php');
	require_once(dirname(__FILE__) . '/tweeter.php');
	require_once(dirname(__FILE__) . '/plugin-admin.php');
  	require_once(dirname(__FILE__) . '/twitter.class.php');
	require_once(dirname(__FILE__) . '/ajax.php');

	$tweeter = new Tweeter_Admin();
}



register_deactivation_hook(__FILE__, 'tweet_manager_deactivation');

function tweet_manager_deactivation() {
	
	$args = array();
	$args['user_id'] = 1;

	wp_clear_scheduled_hook('scheduled_tweet_action', $args);
}



?>