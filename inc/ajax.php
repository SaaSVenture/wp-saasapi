<?

class saas_ajax extends saas_wpapi{

	var $_data;
	
	var $_sitemember_ID;
	var $_ID;
	public static $_table = MEMBERS_TABLE;
	
	function __construct(){
		
	}
	
	public function ajax_init(){

		/* 
		add_action('wp_ajax_ajax_checkemail',array('saas_ajax','checkemail'));
		add_action('wp_ajax_nopriv_ajax_checkemail',array('saas_ajax','checkemail'));
		 */
		add_action('wp_ajax_saas_wpapi_settings_save',array('saas_ajax','settings_save'));
		
	}

	
	
	public function settings_save($data_post=''){
		global $wpdb;
		
		$data = $_POST;
		if(is_array($data_post))
			$data = $data_post;
		
		unset($data['_wp_http_referer'],$data['_wpnonce'],$data['action']);
		update_option('saas-wpapi-settings',$data);
		/* 
        if(username_exists($data['user'])){
            if(!is_array($data_post)){
				echo '1';exit;
			}
            return true;
		} */
		echo true;
        exit;
	}


	
}