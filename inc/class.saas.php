<?php


// Define
DEFINE("HTTP_GET","GET");
DEFINE("HTTP_POST","POST");

class saas_wpapi{


	public function __construct() {
	
	}
	
	public function init() {
		
		global $wpdb;
		
		$_options = get_option('saas-wpapi-settings');

		saas_ajax::ajax_init();
		
		add_action('generate_rewrite_rules', array('saas_wpapi','themes_dir_add_rewrites'));
	}
	
 
	public function themes_dir_add_rewrites() {
	  global $wp_rewrite;
	  $new_non_wp_rules = array(
	    'css/(.*)'       => 'wp-content/plugins/wp-saasapi/assets/js/admin-functions.js',
	  );
	  $wp_rewrite->non_wp_rules += $new_non_wp_rules;
	}

	public function install(){
		global $wp_rewrite, $wpdb;
		
		
		add_filter('rewrite_rules_array', 'saas_wpapi::themes_dir_add_rewrites'); 
		$wp_rewrite->flush_rules( false );
		
		$_sql = array();
		$_sql[] = '
			CREATE TABLE IF NOT EXISTS `'. MEMBERS_TABLE .'` (
			  `ID` bigint(20) NOT NULL,
			  `userID` bigint(11) NOT NULL,
			  `member_accepted` datetime NOT NULL,
			  `level_id` int(11) NOT NULL
			) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;
			';
		$_sql[] = 'ALTER TABLE `'. MEMBERS_TABLE .'` ADD PRIMARY KEY (`ID`);';

		
		$_error = array();
		foreach($_sql as $_query){
			$wpdb->query($_query);
			if(!empty($wpdb->last_error)){
				$_error[] = $wpdb->last_error;
			}
		}
		
		update_option('saas_wpapi_version',saas_wpapi_version);
		
	}
	
	public function uninstall(){
		global $wp_rewrite;
		
		/* add_action('generate_rewrite_rules', function ($wp_rewrite){
			$newrules = self::rewrite();
            $wp_rewrite->rules = $newrules + $wp_rewrite->rules;
		});
		
		add_filter('rewrite_rules_array', 'site_members::removeRules'); 
		$wp_rewrite->flush_rules( false ); */
	}


	
	public function raw($url,$params,$type=HTTP_POST){ // auth_raw
		try{
			$ch = curl_init();
			if($type == HTTP_GET){
				if(!empty($params) && $params){
					$url = trim($url) . '?' . http_build_query($params);
				}
				curl_setopt($ch, CURLOPT_HTTPGET, 1);
			}
			// Populate the data for POST
			if($type == HTTP_POST){
				curl_setopt($ch, CURLOPT_POST, 1);
				if($params) curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
			}

			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
			curl_setopt($ch, CURLOPT_NETRC, false);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
			curl_setopt($ch, CURLOPT_AUTOREFERER , true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_HEADER , true);
			curl_setopt($ch, CURLOPT_VERBOSE, false);
			curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
			if(isset($_SERVER['HTTP_USER_AGENT'])){
				curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
			} else {
				// Handle the useragent like we are Google Chrome
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.X.Y.Z Safari/525.13.');
			}

			$result=curl_exec($ch);

			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($result, 0, $header_size);
			$body = substr($result, $header_size);

			curl_close($ch);
			return $body;
		}catch (Exception $e) {
			error_log($e->getMessage(),1);
			die('Caught exception: '.print_r($e->getMessage(),1) );
		}

	}
}
