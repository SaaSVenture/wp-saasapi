<?

class saas_wpapi_scripts extends saas_wpapi {

	public function __construct(){
	
		//wp_deregister_script( 'jquery'); 
		//wp_register_script( 'jquery', SAAS_WPAPI_URL_PLUG . '/assets/js/jquery-1.11.3.min.js','', '1.11.3');
	
		wp_register_script('admin-functions', SAAS_WPAPI_URL_PLUG.'/assets/js/admin-functions.js',array('jquery'));
	}
	
	public function admin(){
		$clone = new saas_wpapi_scripts();
		//wp_enqueue_style('font-awesome');
		wp_enqueue_script('admin-functions');
	}
	
}