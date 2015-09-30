<?php namespace Saas\Sdk\Tests;

/**
 * API Interface Documentation
 *
 * @author Taufan Adhitya <toopay@taufanaditya.com>
 * @package saas/sdk
 */

use Saas\Sdk\Contracts\ApiInterface;
use Saas\Sdk\Api;
use Saas\Sdk\ResourceObject;
use Saas\Sdk\ResourceCollection;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

use Mockery as M;
use PHPUnit_Framework_TestCase;
use Exception;

class ApiTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Session provider Mock
	 *
	 * @return Symfony\Component\HttpFoundation\Session\SessionInterface
	 */
	public function testSession()
	{
		return new Session(new MockArraySessionStorage());
	}

	/**
	 * Invalid Transport Mock
	 *
	 * @return Saas\Sdk\Contracts\TransportInterface
	 */
	public function testInvalidTransport()
	{
		$mock = M::mock('Saas\\Sdk\\Contracts\\TransportInterface');
		$mock->shouldReceive('getOwnerApp')->once()->andThrow(new Exception('Just wrong!'));

		return $mock;
	}

	/**
	 * Valid Transport Mock
	 *
	 * @return Saas\Sdk\Contracts\TransportInterface
	 */
	public function testTransport()
	{
		$mock = M::mock('Saas\\Sdk\\Contracts\\TransportInterface');
		$mock->shouldReceive('getOwnerApp')->once()->andReturn(new ResourceObject(array(
			'url' => 'foo.com',
			'slug' => 'foo',
			'alias' => 'http://foo.saasapi.com',
		)));
		$mock->shouldReceive('getOwnerAppIdentity')->once()->andReturn(new ResourceObject(array(
			'url' => 'http://saasapi.com/media/brand/background/1',
			'tile' => 0,
			'position' => 'left',
			'background_color' => '#fff',
			'theme_color' => '#000000',
			'overlay_color' => '#fff',
			'button_color' => '#000000',
			'link_color' => '#000000',
		)));
		$mock->shouldReceive('getUser')->once()->andReturn(new ResourceObject(array(
			'name' => 'Mr. Foo',
			'email' => 'foo@foo.com'
		)));
		$mock->shouldReceive('getCompany')->once()->andReturn(new ResourceObject(array(
			'title' => 'FooCorp',
		)));
		$mock->shouldReceive('switchCompany')->once()->andReturn(new ResourceObject(array(
			'title' => 'FooCorpReplacement',
		)));
		$mock->shouldReceive('getCompaniesByUser')->once()->andReturn(new ResourceCollection(array(
			array('title' => 'FooCorp'),
			array('title' => 'BarCorp'),
		)));
		$mock->shouldReceive('getCurrentSubscription')->once()->andReturn(new ResourceObject(array(
			'title' => 'Startup',
			'status' => 'active',
		)));
		$mock->shouldReceive('clearSession')->once();
		$mock->shouldReceive('getPlans')->once()->andReturn(new ResourceCollection(array(
			array('name' => 'Free', 'price' => '0'),
			array('name' => 'Startup', 'price' => '100'),
			array('name' => 'Enterprise', 'price' => '500'),
		)));
		$mock->shouldReceive('getRules')->once()->andReturn(new ResourceCollection(array(
			array('slug' => 'is_admin', 'title' => 'Administrator checker', 'metric_type' => 'system', 'is_deletable' => false),
			array('slug' => 'export_and_printing', 'title' => 'Export/Printing Report', 'metric_type' => 'on_off', 'is_deletable' => true),
		)));
		$mock->shouldReceive('getRule')->once()->andReturn(new ResourceObject(array(
			'slug' => 'is_admin', 
			'title' => 'Administrator checker', 
			'metric_type' => 'system', 
			'is_deletable' => false
		)));
		$mock->shouldReceive('checkAcl')->once()->andReturn(true);

		return $mock;
	}

	/**
	 * Mock invalid API Instance
	 *
	 * @depends testInvalidTransport
	 * @depends testSession
	 * @return Saas\Sdk\Contracts\ApiInterface
	 */
	public function testInvalidApi($transport, $session)
	{
		return Api::factory('some-key', 's0m3s3cr3t', $transport, $session);
	}

	/**
	 * API Instance
	 *
	 * @depends testTransport
	 * @depends testSession
	 * @return Saas\Sdk\Contracts\ApiInterface
	 */
	public function testApi($transport, $session)
	{
		return Api::factory('some-key', 's0m3s3cr3t', $transport, $session);
	}

	/**
	 * API Callback mock
	 *
	 * @void string
	 */
	public function uselessCallback()
	{
		echo 'yay!';
	}

	/**
	 * Original App Getter test
	 *
	 * @depends testInvalidApi
	 * @depends testApi
	 */
	public function testGetOriginalAppUrl($invalidApi, $api)
	{
		// Valid API execution
		$this->assertEquals('http://foo.com', $api->getOriginalAppUrl());

		// Invalid API execution
		$this->setExpectedException('Exception', 'Just wrong!');
		$invalidApi->getOriginalAppUrl();
	}

	/**
	 * Instance Auth URL Getter test
	 *
	 * @depends testInvalidApi
	 * @depends testApi
	 */
	public function testGetLoginUrl($invalidApi, $api)
	{
		$this->assertEquals('http://foo.saasapi.com/auth/login', $api->getLoginUrl());

		// Invalid API execution
		$this->setExpectedException('Exception', 'Just wrong!');
		$invalidApi->getLoginUrl();
	}

	/**
	 * Instance Exchange URL Getter test (non interactive)
	 *
	 * @depends testApi
	 */
	public function testGetExchangeUrlNonInteractive($api)
	{
		// Non interactive
		$resultUrl = $api->getExchangeUrl(1,2,3);
		$resultComps = parse_url($resultUrl);

		$this->assertArrayHasKey('query', $resultComps);

		$resultQuery = $resultComps['query'];
		parse_str($resultQuery);

		$this->assertTrue(isset($key));
		$this->assertTrue(isset($secret));
		$this->assertTrue(isset($interactive));
		$this->assertTrue(isset($user_id));
		$this->assertTrue(isset($company_id));
		$this->assertTrue(isset($session_id));

		$this->assertEquals('some-key', $key);
		$this->assertEquals('s0m3s3cr3t', $secret);
		$this->assertEquals('0', $interactive);
		$this->assertEquals('1', $user_id);
		$this->assertEquals('2', $company_id);
		$this->assertEquals('3', $session_id);
	}

	/**
	 * Instance Exchange URL Getter test (interactive)
	 *
	 * @depends testApi
	 */
	public function testGetExchangeUrlInteractive($api)
	{
		// Interactive mode
		$resultUrl = $api->getExchangeUrl(1,2,3, true);
		$resultComps = parse_url($resultUrl);

		$this->assertArrayHasKey('query', $resultComps);

		$resultQuery = $resultComps['query'];
		parse_str($resultQuery);

		$this->assertTrue(isset($key));
		$this->assertTrue(isset($secret));
		$this->assertTrue(isset($interactive));
		$this->assertTrue(isset($user_id));
		$this->assertTrue(isset($company_id));
		$this->assertTrue(isset($session_id));

		$this->assertEquals('some-key', $key);
		$this->assertEquals('s0m3s3cr3t', $secret);
		$this->assertEquals('1', $interactive);
		$this->assertEquals('1', $user_id);
		$this->assertEquals('2', $company_id);
		$this->assertEquals('3', $session_id);
	}

	/**
	 * Get profile edit
	 *
	 * @depends testApi
	 * @depends testInvalidApi
	 */
	public function testGetProfileUrl($api, $invalidApi)
	{
		$this->assertEquals('http://foo.saasapi.com/user/profile/edit', $api->getProfileUrl());

		$this->setExpectedException('Exception');
		$invalidApi->getProfileUrl();
	}

	/**
	 * Get wallet
	 *
	 * @depends testApi
	 * @depends testInvalidApi
	 */
	public function testGetWalletUrl($api, $invalidApi)
	{
		$this->assertEquals('http://foo.saasapi.com/user/wallet', $api->getWalletUrl());

		$this->setExpectedException('Exception');
		$invalidApi->getWalletUrl();
	}

	/**
	 * Get subscription url
	 *
	 * @depends testApi
	 * @depends testInvalidApi
	 */
	public function testGetSubscriptionUrl($api, $invalidApi)
	{
		$this->assertEquals('http://foo.saasapi.com/brand/subscription', $api->getSubscriptionUrl());

		$this->setExpectedException('Exception');
		$invalidApi->getSubscriptionUrl();
	}

	/**
	 * Get purchase url
	 *
	 * @depends testApi
	 * @depends testInvalidApi
	 */
	public function testGetPurchaseUrl($api, $invalidApi)
	{
		$this->assertEquals('http://foo.saasapi.com/start/Small', $api->getPurchaseUrl('Small'));
		$this->assertEquals('http://foo.saasapi.com/start/Medium', $api->getPurchaseUrl('Medium'));
		$this->assertEquals('http://foo.saasapi.com/start/Large', $api->getPurchaseUrl('Large'));

		$this->setExpectedException('Exception');
		$invalidApi->getPurchaseUrl('Random');
	}

	/**
	 * Get identity
	 *
	 * @depends testApi
	 */
	public function testGetOriginalAppIdentity($api)
	{
		$identity = $api->getOriginalAppIdentity();

		$this->assertInstanceOf('Saas\Sdk\ResourceObject', $identity);
		$this->assertEquals('http://saasapi.com/media/brand/background/1', $identity['url']);
		$this->assertEquals(0, $identity['tile']);
		$this->assertEquals('left', $identity['position']);
		$this->assertEquals('#fff', $identity['background_color']);
		$this->assertEquals('#000000', $identity['theme_color']);
		$this->assertEquals('#fff', $identity['overlay_color']);
		$this->assertEquals('#000000', $identity['button_color']);
		$this->assertEquals('#000000', $identity['link_color']);
	}

	/**
	 * Instance checkSession test
	 *
	 * @depends testApi
	 */
	public function testCheckSession($api)
	{
		// Emulate accepting hash
		$_GET[ApiInterface::SAAS_API_HASH] = md5('some-key');
		$_GET[ApiInterface::SAAS_API_QS_USER] = '1';
		$_GET[ApiInterface::SAAS_API_QS_COMPANY] = '2';
		$_GET[ApiInterface::SAAS_API_QS_SESSION] = '3';

		$callableOne = array($this, 'uselessCallback');
		$callableTwo = function() {
			echo 'Look ma!';
		};

		ob_start();
		$api->checkSession($callableOne);
		$callbackContentOne = ob_get_clean();
		$this->assertEquals('yay!', $callbackContentOne);

		ob_start();
		$api->checkSession($callableTwo);
		$callbackContentTwo = ob_get_clean();
		$this->assertEquals('Look ma!', $callbackContentTwo);
	}

	/**
	 * Login checker and Logout test
	 *
	 * @depends testApi
	 */
	public function testIsLoginLogout($api)
	{
		$api->logout();
		$this->assertFalse($api->isLogin());

		// Emulate accepting hash
		$_GET[ApiInterface::SAAS_API_HASH] = md5('some-key');
		$_GET[ApiInterface::SAAS_API_QS_USER] = '1';
		$_GET[ApiInterface::SAAS_API_QS_COMPANY] = '2';
		$_GET[ApiInterface::SAAS_API_QS_SESSION] = '3';
		$api->checkSession();

		$this->assertTrue($api->isLogin());
	}

	/**
	 * Get User Resource test
	 *
	 * @depends testApi
	 */
	public function testGetUser($api)
	{
		$user = $api->getActiveUser();

		$this->assertInstanceOf('Saas\Sdk\ResourceObject', $user);
		$this->assertEquals('Mr. Foo', $user['name']);
		$this->assertEquals('foo@foo.com', $user['email']);
	}

	/**
	 * Get Company Resource test
	 *
	 * @depends testApi
	 */
	public function testGetCompany($api)
	{
		$company = $api->getActiveCompany();

		$this->assertInstanceOf('Saas\Sdk\ResourceObject', $company);
		$this->assertEquals('FooCorp', $company['title']);
		$this->assertEquals($company, $api->getCompany(1));
	}

	/**
	 * Set Company resource
	 *
	 * @depends testApi
	 */
	public function testSetCompany($api)
	{
		$newCompany = $api->setActiveCompany(2);

		$this->assertInstanceOf('Saas\Sdk\ResourceObject', $newCompany);
		$this->assertEquals('FooCorpReplacement', $newCompany['title']);
	}

	/**
	 * Get Companies Resource test
	 *
	 * @depends testApi
	 */
	public function testGetUserCompanies($api)
	{
		$companies = $api->getActiveUserCompanies();

		$this->assertInstanceOf('Saas\Sdk\ResourceCollection', $companies);

		foreach ($companies as $i => $company) {
			switch ($i) {
				case 0:
					$this->assertEquals('FooCorp',$company['title']);
					break;
				case 1:
					$this->assertEquals('BarCorp',$company['title']);
					break;
			}
		}
	}

	/**
	 * Get active subscription test
	 *
	 * @depends testApi
	 */
	public function testGetActiveSubscription($api)
	{
		$subscription = $api->getActiveSubscription();

		$this->assertInstanceOf('Saas\Sdk\ResourceObject', $subscription);
		$this->assertEquals('active', $subscription['status']);
		$this->assertEquals('Startup', $subscription['title']);
	}

	/**
	 * Get plans
	 *
	 * @depends testApi
	 */
	public function testGetPlans($api)
	{
		$plans = $api->getPlans();

		$this->assertInstanceOf('Saas\Sdk\ResourceCollection', $plans);

		foreach ($plans as $i => $plan) {
			switch ($i) {
				case 0:
					$this->assertEquals('Free',$plan['name']);
					$this->assertEquals(0,$plan['price']);
					break;
				case 1:
					$this->assertEquals('Startup',$plan['name']);
					$this->assertEquals(100,$plan['price']);
					break;
				case 3:
					$this->assertEquals('Enterprise',$plan['name']);
					$this->assertEquals(500,$plan['price']);
					break;
			}
		}
	}

	/**
	 * Get rules
	 *
	 * @depends testApi
	 */
	public function testGetRules($api)
	{
		$rules = $api->getRules();

		$this->assertInstanceOf('Saas\Sdk\ResourceCollection', $rules);

		foreach ($rules as $i => $rule) {
			switch ($i) {
				case 0:
					$this->assertEquals('is_admin', $rule['slug']);
					$this->assertEquals('Administrator checker', $rule['title']);
					$this->assertEquals('system', $rule['metric_type']);
					$this->assertFalse($rule['is_deletable']);
					break;

				case 1:
					$this->assertEquals('export_and_printing', $rule['slug']);
					$this->assertEquals('Export/Printing Report', $rule['title']);
					$this->assertEquals('on_off', $rule['metric_type']);
					$this->assertTrue($rule['is_deletable']);
					break;
			}
		}
	}

	/**
	 * Get rule resource
	 *
	 * @depends testApi
	 */
	public function testGetRule($api)
	{
		$rule = $api->getRule('is_admin');

		$this->assertInstanceOf('Saas\Sdk\ResourceObject', $rule);
		$this->assertEquals('is_admin', $rule['slug']);
		$this->assertEquals('Administrator checker', $rule['title']);
		$this->assertEquals('system', $rule['metric_type']);
		$this->assertFalse($rule['is_deletable']);
	}

	/**
	 * ACL assertion
	 *
	 * @depends testApi
	 */
	public function testIsAllowed($api)
	{
		$this->assertTrue($api->isAllowed('is_admin'));
	}
}