<?php
/*
Plugin Name: Sawfish Connect for Salesforce
Description: Connect, query and present records from your Salesforce database in beautiful card, table, calendar and custom layouts.
Author: Sawfish Plugins
Author URI: https://sfplugin.com
Version: 1.3.19
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/* Pages */
include_once 'adminmenu.php';
include_once 'add_cards.php';

/* short code */
include_once 'shortcode.php';

include_once 'curl.php';
include_once 'response.php';

include_once 'sfsettings.php';

define('sf28c', TRUE);

/* Global Variables */
$sforc_optarray = sf28c_Settings::get();


add_action('admin_init','register_setting_sforc');

add_action('wp_enqueue_scripts', 'sforc_scripts');  

function register_setting_sforc()
{
	register_setting('sforc_settings_group','sf28c');
}


if(is_multisite()) 
{
  add_action('network_admin_menu','sforc_settings_network_menu');
}
else
  add_action('admin_menu','sforc_settings_menu');


function sforc_settings_menu()
{
	add_menu_page('Sawfish Salesforce Connect', 'Sawfish Connect', 'manage_options','sforc-settings', 'sforc_settings_page', 'dashicons-cloud');

	add_submenu_page( 'sforc-settings', 'Add New Layout', 'Add New Layout', 'manage_options', 'new_sforc_cards', 'sforc_add_card' );
}

function sforc_settings_network_menu()
{
  add_menu_page('Sawfish Salesforce Connect', 'Sawfish Connect', 'manage_options','sforc-settings', 'sforc_settings_page', 'dashicons-cloud');

  add_submenu_page( 'sforc-settings', 'Add New Layout', 'Add New Layout', 'manage_options', 'new_sforc_cards', 'sforc_add_card' );
}

register_uninstall_hook( __FILE__, 'sf28c_uninstall' );

function sf28c_uninstall() {

    if(is_multisite()) 
      delete_site_option( 'sf28c' );
    else
      delete_option( 'sf28c' );

}


function sforce_setup_oauth()
{
 	global $sforc_optarray; 

    if(wp_verify_nonce( $_POST['sforc_admin_menu'], 'sforce_setup_oauth' ) && isset($_POST['client_id']) && isset($_POST['client_secret']) && isset($_POST['login_url']))
    {

       $sforc_optarray['client_id'] = sanitize_text_field($_POST['client_id']);
       $sforc_optarray['client_secret'] = sanitize_text_field($_POST['client_secret']);
       $sforc_optarray['login_url'] = sanitize_text_field($_POST['login_url']);
       
       if(is_multisite()) 
           update_network_option( null, 'sf28c', sf28c_Settings::e($sforc_optarray));       
       else
           update_option('sf28c', sf28c_Settings::e($sforc_optarray));      


       $sforc_admin_site_url = '';
       
       if( is_multisite() )
         $sforc_admin_site_url = network_admin_url();
       else
         $sforc_admin_site_url = admin_url();


       $sf_auth_url = "https://".$sforc_optarray['login_url']
             . "/services/oauth2/authorize?response_type=code&client_id="
             . $sforc_optarray['client_id'] 
             . "&redirect_uri=" . urlencode( $sforc_admin_site_url.'admin.php?page=sforc-settings' );

       wp_redirect( $sf_auth_url );
       exit;

    }
 

}

add_action( 'admin_post_sforce_setup_oauth', 'sforce_setup_oauth' );

add_shortcode('showsforce', 'sforc_shortcode_show_records');

add_shortcode('sectionsforce', 'sforc_shortcode_section');



/* Stylesheets and Scripts  */
function sforc_scripts() {
  wp_register_style( 'sforc_cards', plugins_url('css/card.css',__FILE__ ));
  wp_register_style( 'sforc_table', plugins_url('css/table.css',__FILE__ ));
  wp_register_style( 'sforc_calendar', plugins_url('css/calendar.css',__FILE__ ));
  wp_register_style( 'sforc_main', plugins_url('css/main.css',__FILE__ ));

  wp_register_script( 'sforc_moment_js', plugins_url('js/moment.js',__FILE__ ));
  wp_register_script( 'sforc_table_js', plugins_url('js/table.js',__FILE__ ));
  wp_register_script( 'sforc_calendar_js', plugins_url('js/calendar.js',__FILE__ ), array('jquery'), '1', true);
  wp_register_script( 'sforc_list_js', plugins_url('js/list.js',__FILE__ ), array('jquery'), '1', true);

}


