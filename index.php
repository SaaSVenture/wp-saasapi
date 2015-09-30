<?

/*
Plugin Name: SaaS WP API
Plugin URI: 
Description: SaaS WP API
Author: 
Version: 0.15.9.30
Author URI: 
*/

global $wpdb;
define("saas_wpapi_version",'0.15.9.30');//its base on date :P y.m.d
define("SAAS_WPAPI_TABLE",$wpdb->prefix.'-- not yet');
define("SAAS_WPAPI_SECRET",basename(dirname(__FILE__)));
define("SAAS_WPAPI_URL_PLUG",WP_PLUGIN_URL . '/'.basename(dirname(__FILE__)));
define("SAAS_WPAPI_DIR_PLUG",WP_PLUGIN_DIR . '/'.basename(dirname(__FILE__)));

if(!defined('WP_HOME'))
	define("WP_HOME",site_url());

include_once('saas.sdk.php');
include_once('inc/class.saas.php');
require_once('inc/scripts.php');
include_once('inc/ajax.php');

include_once('inc/menu.php');


//require_once('Saas/Sdk/Api.php');

add_action( 'init', 'saas_wpapi::init' );
/* 

include_once('_inc/function.php');

include_once('site-member/class.table.php');
include_once('site-member/screen.php'); 


register_activation_hook( __FILE__, 'site_member_install');
register_deactivation_hook(__FILE__, 'uninstall_site_member');
function site_member_install(){
    site_members::install();
}
function uninstall_site_member(){
	site_members::uninstall();
}
 */