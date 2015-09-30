test mode

<?

//use Saas\Sdk\Api;


$_options = get_option('saas-wpapi-settings');

//$_sdk = new SaasSdk();
// Assuming you're set your API key/secret as $apiKey and $apiSecret variables
//$api = $_sdk->login($_options['api_key'], $_options['api_secret']);

//$api2 = Api::factory($key, $secret);

print_r($api);
print_r($api2);


// Getting your SaaS instance login url
//$loginUrl = $api->getLoginUrl();


print_r($loginUrl);