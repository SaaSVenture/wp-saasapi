<?php

require_once 'vendor/autoload.php';
//echo phpinfo();

use Saas\Sdk\Api;

$apiKey ='5e23feb840736228c748e2015208c643-1zphLE';
$apiSecret='eyJpdiI6ImoyR1hUcTBlOEZicTI1WHBOeFRTOWJSMUNWRCtFTndKMmVjYnA2WTlUS289IiwidmFsdWUiOiJ2aGNpSnExcEJBSEZRaFpIOGVacFZEQmhncGxHQ0tpK0N0aWRWTXo0cEZ2VkxLNTZWUDlCWnJ6Y1NyREI2dW5EUmJpVXgwVzNvMHhFOUJUZWREc1wvRGc9PSIsIm1hYyI6IjZlZTIwZWVjZTczODYzYWFmOWY5ZjkyNzRhYWYxYTA';

$api = Api::factory($apiKey, $apiSecret);

$loginUrl = $api->getLoginUrl();
echo $loginUrl .'<br>';
$purchaseUrl = $api->getPurchaseUrl($selectedPlanTitle); // string, eg : http://saasexample.com/start/Medium

if ($api->isLogin()) {
  // Only doing bellow stuff if the current user already login...
  // ...
  
  echo 'login success';
} else {
  // Do this stuff otherwise
  // ...
  echo 'login first';
}
$api->checkSession(function()  {
  //header("Location: /inbox");
    echo 'session';
});
echo "Original URL :".$api->getOriginalAppUrl();

print_r($purchaseUrl);
echo 'done';

exit;

//echo phpinfo();

//namespace Saas\Sdk;
/* 
include_once('Saas/Sdk/Contracts/ApiInterface.php');
include_once('Saas/Sdk/Api.php');
include_once('Saas/Sdk/Credential.php');

include_once('Guzzle/Client.php');


use Guzzle\Exception\CurlException');

include_once('Saas/Sdk/Contracts/ApiInterface.php');
include_once('Saas/Sdk/Contracts/TransportInterface.php');

include_once('Saas/Sdk/Transports/AbstractTransport.php');
include_once('Saas/Sdk/Transports/LocalTransport.php');
include_once('Saas/Sdk/Transports/RemoteTransport.php');

include_once('Saas/Sdk/ResourceObject.php');
//include_once('Symfony\Component\HttpFoundation\Session\SessionInterface.php');
//include_once('Symfony\Component\HttpFoundation\Session\Session.php');
//include_once('Exception, RuntimeException.php');

// Import the class */



$apiKey ='5e23feb840736228c748e2015208c643-1zphLE';
$apiSecret='eyJpdiI6ImoyR1hUcTBlOEZicTI1WHBOeFRTOWJSMUNWRCtFTndKMmVjYnA2WTlUS289IiwidmFsdWUiOiJ2aGNpSnExcEJBSEZRaFpIOGVacFZEQmhncGxHQ0tpK0N0aWRWTXo0cEZ2VkxLNTZWUDlCWnJ6Y1NyREI2dW5EUmJpVXgwVzNvMHhFOUJUZWREc1wvRGc9PSIsIm1hYyI6IjZlZTIwZWVjZTczODYzYWFmOWY5ZjkyNzRhYWYxYTA';
// Assuming you're set your API key/secret as $apiKey and $apiSecret variables


// Getting your SaaS instance login url
//$loginUrl = $api->getLoginUrl();


echo $apiKey;
print_r($api);