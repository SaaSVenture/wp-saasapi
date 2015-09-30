<?php namespace Saas\Sdk\Transports;

/**
 * Remote transport layer
 *
 * @author Taufan Adhitya <toopay@taufanaditya.com>
 * @package saas/sdk
 */

use Saas\Sdk\Contracts\TransportInterface;
use Saas\Sdk\Credential;
use Saas\Sdk\ResourceObject;
use Saas\Sdk\ResourceCollection;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\CurlException;
use Exception;

class RemoteTransport extends AbstractTransport implements TransportInterface
{
	const API_LIMIT_EXCEEDS = 'API rate limit exceeded or your subscription no longer active.';

	/**
	 * @var Saas\Sdk\Credential
	 */
	private $credential;

	/**
	 * @var GuzzleHttp\Client
	 */
	private $client;

	/**
	 * @var array
	 */
	private $defaultHeaders = array();

	/**
	 * Constructor
	 *
	 * @param Saas\Sdk\Credential
	 */
	public function __construct(Credential $credential)
	{
		$this->credential = $credential;
		$this->defaultHeaders = array(
			'X-Saas-Origin-Domain' => static::getCurrentHost(),
			'Authorization' => 'Basic '.$this->getAuthorizationHash(),
		);

		$this->client = new Client($this->baseUrl());
		$this->checkSsl();
	}

	/**
	 * @{inheritDoc}
	 */
	public function getOwnerApp()
	{
		try {
			$response = $this->client->get('/api/instance', $this->defaultHeaders)->send();
			$brandData = $response->getBody();
			return new ResourceObject(json_decode($brandData,true));
		} catch (Exception $e) {
			return new ResourceObject();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getOwnerAppIdentity()
	{
		try {
			$response = $this->client->get('/api/instance/identity', $this->defaultHeaders)->send();
			$identityData = $response->getBody();
			return new ResourceObject(json_decode($identityData,true));
		} catch (Exception $e) {
			return new ResourceObject();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getUsers()
	{
		try {
			$response = $this->client->get('/api/users', $this->defaultHeaders)->send();
			$userDatas = $response->getBody();
			return new ResourceCollection(json_decode($userDatas,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceCollection();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getCompanies()
	{
		try {
			$response = $this->client->get('/api/companies', $this->defaultHeaders)->send();
			$companiesData = $response->getBody();
			return new ResourceCollection(json_decode($companiesData,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceCollection();
		}	
	}


	/**
	 * @{inheritDoc}
	 */
	public function getUser($id)
	{
		try {
			$response = $this->client->get('/api/user/'.$id, $this->defaultHeaders)->send();
			$userData = $response->getBody();
			return new ResourceObject(json_decode($userData,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceObject();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getCompany($id)
	{
		try {
			$response = $this->client->get('/api/company/'.$id, $this->defaultHeaders)->send();
			$brandData = $response->getBody();
			return new ResourceObject(json_decode($brandData,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceObject();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function switchCompany($userId, $brandId)
	{
		try {
			$response = $this->client->get('/api/switch?'.http_build_query(array('user_id' => $userId, 'brand_id' => $brandId)), $this->defaultHeaders)->send();
			$brandData = $response->getBody();
			return new ResourceObject(json_decode($brandData,true));
		} catch (Exception $e) {
			return new ResourceObject();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getCompaniesByUser($userId = 0, $onlyActive = false)
	{
		try {
			$response = $this->client->get('/api/company?'.http_build_query(array(
				'user_id' => $userId,
				'only_active' => var_export($onlyActive, true),
			)), $this->defaultHeaders)->send();
			$companiesData = $response->getBody();
			return new ResourceCollection(json_decode($companiesData,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}
			
			return new ResourceCollection();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getCurrentSubscription($companyId)
	{
		try {
			$response = $this->client->get('/api/company/'.$companyId.'/subscription', $this->defaultHeaders)->send();
			$subscriptionData = $response->getBody();
			return new ResourceObject(json_decode($subscriptionData,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceObject();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function clearSession($sessionId)
	{
		try {
			$this->client->get('/api/clearsession/'.$sessionId, $this->defaultHeaders)->send();
		} catch (Exception $e) {
			// Supress any error
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getPlans()
	{
		try {
			$response = $this->client->get('/api/plans', $this->defaultHeaders)->send();
			$plansData = $response->getBody();
			return new ResourceCollection(json_decode($plansData,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceCollection();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getRules()
	{
		try {
			$response = $this->client->get('/api/rules', $this->defaultHeaders)->send();
			$rulesData = $response->getBody();
			return new ResourceCollection(json_decode($rulesData,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceCollection();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getRule($slug = null)
	{
		try {
			$response = $this->client->get('/api/rules/'.$slug, $this->defaultHeaders)->send();
			$ruleData = $response->getBody();
			return new ResourceObject(json_decode($ruleData,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceObject();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function checkAcl($rule = null, 
							ResourceObject $user = null,
							ResourceObject $company = null,
							ResourceObject $subscription = null)
	{
		try {
			$response = $this->client->get('/api/acl?'.http_build_query(array(
				'rule' => $rule,
				'user_id' => $user->id,
				'company_id' => $company->id,
				'subscription_id' => $subscription->id,
			)), $this->defaultHeaders)->send();
			$aclData = $response->getBody();
			$aclResult = json_decode($aclData);
			return (bool) $aclResult->is_allowed;
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return false;
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getMessageStats($userId)
	{
		try {
			$response = $this->client->get('/api/userstats/message/'.$userId, $this->defaultHeaders)->send();
			$statsData = $response->getBody();
			return new ResourceObject(json_decode($statsData,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceObject();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getInboxByUser($userId, $perPage, $page)
	{
		try {
			$response = $this->client->get('/api/inbox/'.$userId.'/'.$perPage.'/'.$page, $this->defaultHeaders)->send();
			$inboxData = $response->getBody();
			return new ResourceCollection(json_decode($inboxData,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceCollection();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getOutboxByUser($userId, $perPage, $page)
	{
		try {
			$response = $this->client->get('/api/outbox/'.$userId.'/'.$perPage.'/'.$page, $this->defaultHeaders)->send();
			$outboxData = $response->getBody();
			return new ResourceCollection(json_decode($outboxData,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceCollection();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function sendUserMessage($fromId, $toId, $subject, $message)
	{
		try {
			$data = array(
				'from_user' => $fromId,
				'to_user' => $toId,
				'subject' => $subject,
				'body' => $message,
			);
			$response = $this->client->get('/api/message/0?'.http_build_query($data), $this->defaultHeaders)->send();
			$message = $response->getBody();
			return new ResourceObject(json_decode($message,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceObject();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function getMessage($id, $markAsRead = true)
	{
		try {
			$response = $this->client->get('/api/message/'.$id.'?as_read='.var_export($markAsRead, true), $this->defaultHeaders)->send();
			$message = $response->getBody();
			return new ResourceObject(json_decode($message,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceObject();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function deleteMessage($id)
	{
		try {
			$response = $this->client->get('/api/message/'.$id.'?delete=true', $this->defaultHeaders)->send();
			$message = $response->getBody();
			return new ResourceObject(json_decode($message,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceObject();
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function performBatchMessages($operation, $ids)
	{
		try {
			$response = $this->client->get('/api/messages/'.$operation.'?'.http_build_query(compact('ids')), $this->defaultHeaders)->send();
			$ids = $response->getBody();
			return new ResourceObject(json_decode($ids,true));
		} catch (Exception $e) {
			if ($this->isNotAllowed($e)) {
				throw new Exception(self::API_LIMIT_EXCEEDS);
			}

			return new ResourceObject();
		}
	}

	/**
	 * Get the base Saas API url
	 *
	 * @param mixed
	 * @return string
	 */
	protected function baseUrl($path = '')
	{
		return 'https://'.static::getApiRoot().$path;
	}

	/**
	 * Check SSL 
	 *
	 * @return void
	 */
	protected function checkSsl()
	{
		try {
			$this->client->get('/')->send()->getBody();
		} catch (CurlException $e) {
			if ($e->getErrorNo() == 51) {
				// Strict hosting detected, skip ssl host verifier
				$this->client->setSslVerification(false);
			}
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/**
	 * Get Authorization hash
	 *
	 * @param string
	 */
	protected function getAuthorizationHash()
	{
		return base64_encode($this->credential->getKey().':'.$this->credential->getSecret());
	}

	/**
	 * Check exception details
	 *
	 * @param Exception
	 * @return bool
	 */
	protected function isNotAllowed(Exception $e) 
	{
		$message = $e->getMessage();
		return strpos($message, '[status code] 403') !== false;
	}
}