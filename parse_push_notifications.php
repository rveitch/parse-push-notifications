<?php
/*
Plugin Name: FCC Parse Push Notification Tester
Plugin URI: https://github.com/rveitch/parse-push-notifications
Description: This plugin allows you to send Parse.com Push Notifications directly from your WordPress admin dashboard.
Author:  Ryan Veitch
Author URI: http://veitchdigital.com/
Version: 1.0.0
License: GPLv2 or later
*/

/*************************** PARSE SDK ****************************************
*******************************************************************************
* Include and initialize the Parse PHP SDK autoloader.
*/

 //$pn_app_id = 'u1lsufPr8IKCGaMBObC8v2Q1Hue2zMAWzEmtWw1N';
 //$rest_key = 'gauLX0IEvh2wSby9Fs8jq3MjkBMlh115Bad15va2';
 //$master_key = 'RQ5MYea3oZepdFLRiUp1b0s6Scj2DNdszEG0GnRB';

 $app_id = get_option('pn_app_id');
 $rest_key = get_option('pn_app_masterkey');
 $master_key = get_option('pn_app_restkey');

 require('includes/parse-php-sdk-master/autoload.php');
 // Add the "use" declarations where you'll be using the classes
 use Parse\ParseClient;
 //use Parse\ParseObject;
 use Parse\ParseQuery;
 //use Parse\ParseACL;
 use Parse\ParsePush;
 //use Parse\ParseUser;
 use Parse\ParseInstallation;
 //use Parse\ParseException;
 //use Parse\ParseAnalytics;
 //use Parse\ParseFile;
 //use Parse\ParseCloud;

 ParseClient::initialize( $app_id, $rest_key, $master_key );


/*************************** PARSE API FUNCTIONS *******************************
********************************************************************************
* Parse.com API functions begin here.
*/

function parse_push_notifications_send($message){

	//$data = array("alert" => $message);

    ParsePush::send(array(
      "channels" => array( "Ryan" ),
      data => array(
        "alert" => $message,
      )
    ));

  // Notification for iOS users
//  $queryIOS = ParseInstallation::query();
//  $queryIOS->equalTo('deviceType', 'ios');

//  ParsePush::send(array(
//    "where" => $queryIOS,
//    "data" => array(
//      "alert" => $message
//    )
//  ));

  }


/*************************** PARSE-PN Admin Dashboard Menu ********************
*******************************************************************************
* Admin dashboard menu functions.
*/
add_action('admin_menu', 'parse_push_notifications_admin_pages');

function parse_push_notifications_admin_pages() {
	add_menu_page(
                'Parse Push Notifications',
                'Parse Push Notifications',
                'manage_options',
                'parse_push_notifications',
                'parse_push_notifications_options_page',
                'dashicons-share-alt2',
                40 );

  add_action('admin_init', 'wp_parse_pn_admin_init');
}

function wp_parse_pn_admin_init() {
  //register our settings
  register_setting('wp-parse-pn-settings-group', 'pn_app_id');
  register_setting('wp-parse-pn-settings-group', 'pn_app_masterkey');
  register_setting('wp-parse-pn-settings-group', 'pn_app_restkey');
}

/*************************** PARSE-PN Dashboard Page **************************
*******************************************************************************
* Create admin dashboard page.
*/

function parse_push_notifications_options_page() {
	echo'<div class="wrap"><div class="card"><div class="inside">';
	parse_push_notifications_create_form();
	echo '</div></div></div>';

  ?>
  <div class="wrap">
  <div class="card">
    <div class="inside">
	<form action="options.php" method="post">
		<?php settings_fields('wp-parse-pn-settings-group'); ?>

		<h3>Parse API App Settings</h3>

		<table class="form-table">
			<tr valign="top">
				<th style="width:125px" scope="row">Application ID: </th>
				<td><input type="text" name="pn_app_id" value="<?php echo get_option('pn_app_id'); ?>" size="50"></td>
			</tr>
			<tr valign="top">
				<th style="width:125px" scope="row">Master Key: </th>
				<td><input type="text" name="pn_app_masterkey" value="<?php echo get_option('pn_app_masterkey'); ?>" size="50"></td>
			</tr>
			<tr valign="top">
				<th style="width:125px" scope="row">REST API Key: </th>
				<td><input type="text" name="pn_app_restkey" value="<?php echo get_option('pn_app_restkey'); ?>" size="50"></td>
			</tr>
		</table>

		<?php submit_button(); ?>

	</form>
  </div>
  </div>
  </div>
  <?php

}

/*************************** FORM: Push Notifications *************************
*******************************************************************************
* Create form function.
*/

function parse_push_notifications_create_form(){


	if (isset($_POST['parse_push_notifications_push_btn']))
	{
	   if ( function_exists('current_user_can') &&
			!current_user_can('manage_options') )
				die ( _e('Hacker?', 'parse_push_notifications') );

		if (function_exists ('check_admin_referer') )
			check_admin_referer('parse_push_notifications_form');

        parse_push_notifications_send($_POST['pn_text']);

	}

  ?>
		<div id="pn_form">
      <h2>Send a Parse Push Notification</h2>
      <form id="push_form" name="parse_push_notifications" method="post" action=" <?php $_SERVER['PHP_SELF'] ?> ?page=parse_push_notifications&amp;updated=true">
  <?php
    if (function_exists ('wp_nonce_field') )
    wp_nonce_field('parse_push_notifications_form');
  ?>
        <div>
          <p><input type="text" name="pn_text" placeholder="Enter push notification text here" size="70" maxlength="255" /></p>
        </div>
        <div>
          <input type="submit" id="push_button" class="button-primary" name="parse_push_notifications_push_btn" value="Send to all iOS devices"/>
        </div>
      </form>
		</div>
		<?php
}

/*----------------------------------*/
?>
