<?
add_action('admin_menu', 'saas_wpapi_menu');

function saas_wpapi_menu(){
	global $saas_wpapi_screen_manage;
	add_menu_page('Saas WP API','Saas WP API','administrator','saas-wpapi','saas_wpapi_ui','dashicons-admin-generic');

	$saas_wpapi_screen = add_submenu_page('saas-wpapi','Saas Overview','Saas Overview','administrator','saas-wpapi','saas_wpapi_ui');
	add_submenu_page('saas-wpapi','Settings & Config','Settings & Config','administrator','saas-wpapi-settings','saas_wpapi_settings_ui');
	add_submenu_page('saas-wpapi','test','test Config','administrator','saas-wpapi-settings_test','saas_wpapi_settings_ui_test');
	
	
	if(isset($_GET['page'])){
		if(strrpos($_GET['page'],'saas-wpapi-list') !== false){
			if(isset($_GET['action'])== 'add'){
				add_action('admin_print_scripts',array('member_scripts','member_form_add'));
			}
		}else{
			if(is_admin()){
				if(strrpos($_GET['page'],'saas-wpapi') !== false){
					add_action('admin_print_scripts',array('saas_wpapi_scripts','admin'));
				}
			}
		}
		
	}

	
}
/* 
function saas_wpapi_manage_ui(){
	switch ($_GET['action']) {
		case 'add':
			saas_wpapi_add_ui();break;
		case 'edit':
			saas_wpapi_edit_ui();break;
		case 'view':
			saas_wpapi_view_ui();break;
		default:
			require_once(MEMBERS_DIR_PLUG .'/saas-wpapi/list.php');break;
	}
	//require_once('home-overview.php');
}

function saas_wpapi_ui(){
	switch ($_GET['action']) {
		case 'add':
			saas_wpapi_add_ui();break;
		case 'edit':
			saas_wpapi_edit_ui();break;
		case 'view':
			saas_wpapi_view_ui();break;
		default:
			require_once(MEMBERS_DIR_PLUG .'/saas-wpapi/home-overview.php');break;
	}
	//require_once('home-overview.php');
}
function saas_wpapi_add_ui(){require_once(MEMBERS_DIR_PLUG.'/saas-wpapi/add.php');}
function saas_wpapi_edit_ui(){require_once('saas-wpapi/edit.php');}
function saas_wpapi_view_ui(){require_once('saas-wpapi/view.php');}



 */
 
 function saas_wpapi_settings_ui(){require_once(SAAS_WPAPI_DIR_PLUG.'/view/settings.php');}
 function saas_wpapi_settings_ui_test(){include_once(SAAS_WPAPI_DIR_PLUG.'/test.php');}
 
 
 
 