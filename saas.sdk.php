<?



include_once('Saas\Sdk\Contracts\ApiInterface.php');
include_once('Saas\Sdk\Api.php');
include_once('Saas\Sdk\Contracts\TransportInterface.php');
include_once('Saas\Sdk\Transports\AbstractTransport.php');
include_once('Saas\Sdk\Transports\LocalTransport.php');
include_once('Saas\Sdk\Transports\RemoteTransport.php');
include_once('Saas\Sdk\Credential.php');
include_once('Saas\Sdk\ResourceObject.php');


class SaasSdk {

	/**
	 * @var Saas\Sdk\Contracts\ApiInterface
	 */
	protected $api;

	public function __construct(array $config = array())
	{
				// If there is no parameter passed, we'll use the default one
		/* // @see application/config/api.php
		if (empty($config)) {
			$CI =& get_instance();
			$sandbox_url = $CI->config->item('api_sandbox_url');
			if (strpos($sandbox_url, $CI->input->server('HTTP_HOST')) !== false) {
				$config = array(
					'key' => $CI->config->item('api_sandbox_key'),
					'secret' => $CI->config->item('api_sandbox_secret'),
				);
			} else {
				// Use LIVE config
				$config = array(
					'key' => $CI->config->item('api_key'),
					'secret' => $CI->config->item('api_secret'),
				);
			}
		}

		$this->api = Api::factory($config['key'], $config['secret'], null, get_instance()->session);
		$this->api->checkSession(); */
	}
	
	public function login($key='',$secret=''){
		//$this->api = Api::factory($key, $secret);
		//return $this->api;
	}
	

	/**
	 * Overider
	 */
	public function __call($method, $args)
	{
		return call_user_func_array(array($this->api,$method), $args);
	}
} 


