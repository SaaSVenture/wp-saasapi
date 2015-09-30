<?php namespace Saas\Sdk;

/**
 * Main API
 *
 * @author Taufan Adhitya <toopay@taufanaditya.com>
 * @package saas/sdk
 */

use Saas\Sdk\Contracts\ApiInterface;
use Saas\Sdk\Contracts\TransportInterface;
use Saas\Sdk\Transports\AbstractTransport;
use Saas\Sdk\Transports\LocalTransport;
use Saas\Sdk\Transports\RemoteTransport;
use Saas\Sdk\Credential;
use Saas\Sdk\ResourceObject;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Exception, RuntimeException;

final class Api implements ApiInterface
{
	/**
	 * API credential
	 *
	 * @var Saas\Sdk\Credential
	 */
	private $credential;

	/**
	 * API  transport
	 *
	 * @var Saas\Sdk\Contracts\TransportInterface
	 */
	private $transport;

	/**
	 * API session
	 *
	 * @var Symfony\Component\HttpFoundation\Session\SessionInterface
	 */
	private $session;

	/**
	 * API Repositories
	 *
	 * @var array
	 */
	private $repos = array();

	/**
	 * Main API factory
	 *
	 * @param string API Key
	 * @param string API Secret
	 * @param Saas\Sdk\TransportInterface
	 * @param Symfony\Component\HttpFoundation\Session\SessionInterface
	 * @return Saas\Sdk\Contracts\ApiInterface
	 */
	final public static function factory($key, $secret, TransportInterface $transport = null, SessionInterface $session = null)
	{
		return new static($key, $secret, $transport, $session);
	}

	/**
	 * Constructor
	 *
	 * @param string API Key
	 * @param string API Secret
	 * @param Saas\Sdk\TransportInterface
	 * @param Symfony\Component\HttpFoundation\Session\SessionInterface
	 */
	public function __construct($key, $secret, TransportInterface $transport = null, SessionInterface $session = null)
	{
		// Set credential
		$this->credential = new Credential($key, $secret);

		// Set transport
		$this->transport = $transport;
		// Pick appropriate transport, if it wasn't provided
		// @codeCoverageIgnoreStart
		if (!$this->transport) {
			if (strpos(AbstractTransport::getCurrentHost(), AbstractTransport::getApiRoot()) !== false
				&& strpos(AbstractTransport::getCurrentHost(), AbstractTransport::getApiDevRoot()) === false) {
				$this->transport = new LocalTransport($this->credential);
			} else {
				$this->transport = new RemoteTransport($this->credential);
			}
		}
		// @codeCoverageIgnoreEnd

		// Set session
		$this->session = $session;
		// Pick appropriate session, if it wasn't provided
		// @codeCoverageIgnoreStart
		if (!$this->session) {
			$this->session = new Session();
			try {
				$this->session->isStarted() or $this->session->start();
			} catch (Exception $e) {}
		}
		// @codeCoverageIgnoreEnd
	}

	/**
	 * @{inheritDoc}
	 */
	public function getOriginalAppUrl()
	{
		try {
			$app = $this->transport->getOwnerApp();
		} catch (Exception $e) {
			throw new RuntimeException($e->getMessage());
		}

		return self::SAAS_API_HTTP_SCHEME.$app->url;
	}

	/**
	 * @{inheritDoc}
	 */
	public function getLoginUrl()
	{
		return $this->getAppUrl('/auth/login');
	}

	/**
	 * @{inheritDoc}
	 */
	public function getProfileUrl()
	{
		return $this->getAppUrl('/user/profile/edit');
	}

	/**
	 * @{inheritDoc}
	 */
	public function getWalletUrl()
	{
		return $this->getAppUrl('/user/wallet');
	}

	/**
	 * @{inheritDoc}
	 */
	public function getSubscriptionUrl()
	{
		return $this->getAppUrl('/brand/subscription');
	}

	/**
	 * @{inheritDoc}
	 */
	public function getExchangeUrl($userId = null, $companyId = null, $sessionId = null, $interactiveMode = false)
	{
		// Main payload, API key and secret
		$payload = array('key' => $this->credential->getKey(), 'secret' => $this->credential->getSecret(), 'interactive' => $interactiveMode);

		// User id and Company id (active)
		if (!empty($userId)) {
			$payload['user_id'] = $userId;
		}
		if (!empty($companyId)) {
			$payload['company_id'] = $companyId;
		}

		// Session id
		if (!empty($sessionId)) $payload['session_id'] = $sessionId;

		return self::SAAS_API_HTTP_SCHEME
				.AbstractTransport::getApiRoot()
				.'/exchange?'.http_build_query($payload);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getPurchaseUrl($plan)
	{
		return $this->getAppUrl('/start/'.$plan);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getOriginalAppIdentity()
	{
		return $this->transport->getOwnerAppIdentity();
	}

	/**
	 * @{inheritDoc}
	 */
	public function checkSession($onSuccessCallback = null)
	{
		if (isset($_GET[self::SAAS_API_HASH])) {
			$hash = $_GET[self::SAAS_API_HASH];
			if ($hash == md5($this->credential->getKey())) {
				// Set current session
				$this->session->set(self::SAAS_API_LOGIN, true);
				$this->session->set(self::SAAS_API_SESSION, $_GET[self::SAAS_API_QS_SESSION]);
				$this->session->set(self::SAAS_API_USER, $_GET[self::SAAS_API_QS_USER]);
				$this->session->set(self::SAAS_API_COMPANY, $_GET[self::SAAS_API_QS_COMPANY]);

				if (is_callable($onSuccessCallback)) {
					call_user_func($onSuccessCallback);
				} 
			}
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function isLogin()
	{
		return $this->session->get(self::SAAS_API_LOGIN, false);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getActiveUser()
	{
		if (isset($this->repos[self::SAAS_API_ACTIVE_USER])) {
			return $this->repos[self::SAAS_API_ACTIVE_USER];
		}

		$activeUser = $this->getUser($this->session->get(self::SAAS_API_USER, 0));
		$this->repos[self::SAAS_API_ACTIVE_USER] = $activeUser;

		return $activeUser;
	}

	/**
	 * @{inheritDoc}
	 */
	public function getActiveCompany()
	{
		if (isset($this->repos[self::SAAS_API_ACTIVE_COMPANY])) {
			return $this->repos[self::SAAS_API_ACTIVE_COMPANY];
		}

		$companies = $this->getUserCompanies($this->session->get(self::SAAS_API_USER, 0), true);
		$activeCompany = $companies->getIterator()->current();

		if (!isset($activeCompany)) {
			$activeCompany = new ResourceObject();
		}

		$this->repos[self::SAAS_API_ACTIVE_COMPANY] = $activeCompany;

		return $activeCompany;
	}

	/**
	 * @{inheritDoc}
	 */
	public function setActiveCompany($id)
	{
		return $this->transport->switchCompany($this->session->get(self::SAAS_API_USER, 0), $id);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getActiveUserCompanies()
	{
		return $this->getUserCompanies($this->session->get(self::SAAS_API_USER, 0));
	}

	/**
	 * @{inheritDoc}
	 */
	public function getActiveSubscription()
	{
		if (isset($this->repos[self::SAAS_API_ACTIVE_SUBSCRIPTION])) {
			return $this->repos[self::SAAS_API_ACTIVE_SUBSCRIPTION];
		}

		$activeSubscription = $this->transport->getCurrentSubscription($this->session->get(self::SAAS_API_COMPANY, 0));
		$this->repos[self::SAAS_API_ACTIVE_SUBSCRIPTION] = $activeSubscription;

		return $activeSubscription;
	}

	/**
	 * @{inheritDoc}
	 */
	public function getUsers()
	{
		return $this->transport->getUsers();
	}

	/**
	 * @{inheritDoc}
	 */
	public function getCompanies()
	{
		return $this->transport->getCompanies();
	}

	/**
	 * @{inheritDoc}
	 */
	public function getUser($id = 0)
	{
		return $this->transport->getUser($id);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getCompany($id = 0)
	{
		return $this->transport->getCompany($id);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getUserCompanies($userId = 0, $onlyActive = false)
	{
		return $this->transport->getCompaniesByUser($userId, $onlyActive);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getUserMessageStats($userId)
	{
		return $this->transport->getMessageStats($userId);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getUserInbox($userId, $perPage = 10, $page = 1)
	{
		if (isset($this->repos[self::SAAS_API_USER_INBOX])
			&& isset($this->repos[self::SAAS_API_USER_INBOX][$userId]) 
			&& isset($this->repos[self::SAAS_API_USER_INBOX][$userId][$page]) 
			&& isset($this->repos[self::SAAS_API_USER_INBOX][$userId][$page][$perPage])) {
			return $this->repos[self::SAAS_API_USER_INBOX][$userId][$page][$perPage];
		}

		$inbox = $this->transport->getInboxByUser($userId, $perPage, $page);

		if (!isset($this->repos[self::SAAS_API_USER_INBOX][$userId])) {
			$this->repos[self::SAAS_API_USER_INBOX][$userId] = array();
		}
		if (!isset($this->repos[self::SAAS_API_USER_INBOX][$userId][$page])) {
			$this->repos[self::SAAS_API_USER_INBOX][$userId][$page] = array();
		}
		$this->repos[self::SAAS_API_USER_INBOX][$userId][$page][$perPage] = $inbox;

		return $inbox;
	}

	/**
	 * @{inheritDoc}
	 */
	public function getUserOutbox($userId, $perPage = 10, $page = 1)
	{
		if (isset($this->repos[self::SAAS_API_USER_OUTBOX])
			&& isset($this->repos[self::SAAS_API_USER_OUTBOX][$userId]) 
			&& isset($this->repos[self::SAAS_API_USER_OUTBOX][$userId][$page]) 
			&& isset($this->repos[self::SAAS_API_USER_OUTBOX][$userId][$page][$perPage])) {
			return $this->repos[self::SAAS_API_USER_OUTBOX][$userId][$page][$perPage];
		}

		$outbox = $this->transport->getOutboxByUser($userId, $perPage, $page);

		if (!isset($this->repos[self::SAAS_API_USER_OUTBOX][$userId])) {
			$this->repos[self::SAAS_API_USER_OUTBOX][$userId] = array();
		}
		if (!isset($this->repos[self::SAAS_API_USER_OUTBOX][$userId][$page])) {
			$this->repos[self::SAAS_API_USER_OUTBOX][$userId][$page] = array();
		}
		$this->repos[self::SAAS_API_USER_OUTBOX][$userId][$page][$perPage] = $outbox;

		return $outbox;
	}

	/**
	 * @{inheritDoc}
	 */
	public function sendMessage($fromId, $toId, $subject, $message)
	{
		return $this->transport->sendUserMessage($fromId, $toId, $subject, $message);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getMessage($id, $markAsRead = true)
	{
		return $this->transport->getMessage($id, $markAsRead);
	}

	/**
	 * @{inheritDoc}
	 */
	public function deleteMessage($id)
	{
		return $this->transport->deleteMessage($id);
	}

	/**
	 * @{inheritDoc}
	 */
	public function batchMessages($operation, $ids)
	{
		return $this->transport->performBatchMessages($operation, $ids);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getPlans()
	{
		return $this->transport->getPlans();
	}

	/**
	 * @{inheritDoc}
	 */
	public function getRules()
	{
		return $this->transport->getRules();
	}

	/**
	 * @{inheritDoc}
	 */
	public function getRule($slug = null)
	{
		return $this->transport->getRule($slug);
	}

	/**
	 * @{inheritDoc}
	 */
	public function isAllowed($rule = null)
	{
		return $this->transport->checkAcl($rule, $this->getActiveUser(), $this->getActiveCompany(), $this->getActiveSubscription());
	}

	/**
	 * @{inheritDoc}
	 */
	public function logout()
	{
		$sessionId = $this->session->get(self::SAAS_API_SESSION);
		$this->session->remove(self::SAAS_API_LOGIN);
		$this->session->remove(self::SAAS_API_SESSION);
		$this->session->remove(self::SAAS_API_USER);
		$this->session->remove(self::SAAS_API_COMPANY);

		$this->transport->clearSession($sessionId);
	}

	/**
	 * Common App URL Generator
	 *
	 * @param string Path
	 * @return string Full URI
	 */
	protected function getAppUrl($path)
	{
		try {
			$app = $this->transport->getOwnerApp();
		} catch (Exception $e) {
			throw new RuntimeException($e->getMessage());
		}

		$state = '';
		// @codeCoverageIgnoreStart
		if ($app->sandbox_key == $this->credential->getKey()
			|| (AbstractTransport::hasHost() && strpos(AbstractTransport::getCurrentHost(), AbstractTransport::getApiDevRoot()) !== false)) {
			$state = '?'.http_build_query(array(
				'key' => $this->credential->getKey(),
				'secret' => $this->credential->getSecret(),
			));
		}
		// @codeCoverageIgnoreEnd

		return $app->alias.$path.$state;
	}
}