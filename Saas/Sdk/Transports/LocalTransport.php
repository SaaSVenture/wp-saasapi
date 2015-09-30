<?php namespace Saas\Sdk\Transports;

/**
 * Local transport layer
 *
 * @author Taufan Adhitya <toopay@taufanaditya.com>
 * @package saas/sdk
 */

use Saas\Sdk\Contracts\TransportInterface;
use Saas\Sdk\Credential;
use Saas\Sdk\ResourceObject;
use RuntimeException;

class LocalTransport extends AbstractTransport implements TransportInterface
{
	/**
	 * @var Saas\Sdk\Credential
	 */
	private $credential;

	/**
	 * Constructor
	 *
	 * @param Saas\Sdk\Credential
	 */
	public function __construct(Credential $credential)
	{
		$this->credential = $credential;
	}

	/**
	 * @{inheritDoc}
	 */
	public function getOwnerApp()
	{
		$brand = $this->getApiDBGateway()
					->table('brands')
					->where('key', $this->credential->getKey())
					->where('secret', $this->credential->getSecret())
					->first();

		if ($brand) {
			$brand->alias = $this->getAlias();
		}

		return new ResourceObject($brand);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getOwnerAppIdentity()
	{
		$identity = $this->getApiDBGateway()
					->table('themes')
            		->join('brands', 'brands.id', '=', 'themes.instance_id')
            		->join('attachment', 'attachment.id', '=', 'themes.attachment_id')
					->where('themes.instance_type', 'Brand')
					->where('brands.key', $this->credential->getKey())
					->where('brands.secret', $this->credential->getSecret())
            		->select('themes.tile', 'themes.position', 'themes.background_color', 'themes.theme_color', 'themes.instance_id',
            				'themes.overlay_color', 'themes.button_color', 'themes.link_color', 'attachment.url')
					->first();

		if ( ! $identity) {
			$identity = $this->getApiDBGateway()
					->table('themes')
            		->join('brands', 'brands.id', '=', 'themes.instance_id')
					->where('themes.instance_type', 'Brand')
					->where('brands.key', $this->credential->getKey())
					->where('brands.secret', $this->credential->getSecret())
            		->select('themes.tile', 'themes.position', 'themes.background_color', 'themes.theme_color', 'themes.instance_id',
            				'themes.overlay_color', 'themes.button_color', 'themes.link_color')
					->first();
		}

		if ($identity) {
			// Attach logo
			$identity['logo_url'] = 'http://'. static::getApiRoot().'/media/brand/logo/'.$identity['instance_id'].'?size=small';
			unset($identity['instance_id']);

		} else {
			// No themes yet
			$rootIdentity = $this->getApiDBGateway()
					->table('brands')
					->where('key', $this->credential->getKey())
					->where('secret', $this->credential->getSecret())
					->first();

			if ($rootIdentity) {
				$identity = array(
					'logo_url' => 'http://'. static::getApiRoot().'/media/brand/logo/'.$rootIdentity['id'].'?size=small',
				);
			}
		}

		return new ResourceObject($identity);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getUsers()
	{
		$users = $this->getApiDBGateway()
					->table('users')
					->whereNull('users.deleted_at')
					->get();

		return new ResourceCollection($users);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getCompanies()
	{
		$brands = $this->getApiDBGateway()
					->table('brands')
					->join('subscriptions', 'subscriptions.brand_id', '=', 'brands.id')
					->join('subscription_plans', 'subscription_plans.id', '=', 'subscriptions.plan_id')
					->whereNull('brands.deleted_at')
					->whereNull('subscriptions.deleted_at')
					->where('subscriptions.status', 'active')
					->select('brands.*')
					->get();

		return new ResourceCollection($brands);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getUser($id)
	{
		$user = $this->getApiDBGateway()
					->table('users')
					->where('id', $id)
					->first();

		return new ResourceObject($user);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getCompany($id)
	{
		$company = $this->getApiDBGateway()
						->table('brands')
						->where('id', $id)
						->first();

		return new ResourceObject($company);
	}

	/**
	 * @{inheritDoc}
	 */
	public function switchCompany($userId, $brandId)
	{
		throw new Exception('Not implemented');
	}

	/**
	 * @{inheritDoc}
	 */
	public function getCompaniesByUser($userId = 0, $onlyActive = false)
	{
		$query = $this->getApiDBGateway()
						->table('brands')
						->join('group_user_brand', 'brands.id', '=', 'group_user_brand.brand_id')
						->where('group_user_brand.user_id', $userId)
						->whereNull('group_user_brand.deleted_at');

		if ($onlyActive) $query = $query->where('group_user_brand.active', 1);
		
		$companies = $query->get();

		return new ResourceCollection($companies);
	}

	/**
	 * @{inheritDoc}
	 */
	public function getCurrentSubscription($companyId)
	{
		$subscription = $this->getApiDBGateway()
							->table('subscriptions')
							->where('brand_id', $companyId)
							->whereIn('status', array('active','suspended','pending','expired'))
							->orderBy('status', 'asc')
							->first();

		return new ResourceObject($subscription);
	}

	/**
	 * @{inheritDoc}
	 */
	public function clearSession($sessionId)
	{
		throw new Exception('Not implemented');
	}

	/**
	 * @{inheritDoc}
	 */
	public function getPlans()
	{
		throw new Exception('Not implemented');
	}

	/**
	 * @{inheritDoc}
	 */
	public function getRules()
	{
		throw new Exception('Not implemented');
	}

	/**
	 * @{inheritDoc}
	 */
	public function getRule($slug = null)
	{
		throw new Exception('Not implemented');
	}

	/**
	 * @{inheritDoc}
	 */
	public function checkAcl($rule = null, 
							ResourceObject $user = null,
							ResourceObject $company = null,
							ResourceObject $subscription = null)
	{
		throw new Exception('Not implemented');
	}

	/**
	 * @{inheritDoc}
	 */
	public function getMessageStats($userId)
	{
		throw new Exception('Not implemented');
	}

	/**
	 * @{inheritDoc}
	 */
	public function getInboxByUser($userId, $perPage, $page)
	{
		throw new Exception('Not implemented');
	}

	/**
	 * @{inheritDoc}
	 */
	public function getOutboxByUser($userId, $perPage, $page)
	{
		throw new Exception('Not implemented');
	}

	/**
	 * @{inheritDoc}
	 */
	public function sendUserMessage($fromId, $toId, $subject, $message)
	{
		throw new Exception('Not implemented');
	}

	/**
	 * @{inheritDoc}
	 */
	public function getMessage($id, $markAsRead = true)
	{
		throw new Exception('Not implemented');
	}

	/**
	 * @{inheritDoc}
	 */
	public function deleteMessage($id)
	{
		throw new Exception('Not implemented');
	}

	/**
	 * @{inheritDoc}
	 */
	public function performBatchMessages($operation, $ids)
	{
		throw new Exception('Not implemented');
	}

	/**
	 * Get DB Gateway
	 *
	 * @return Laravel DB
	 */
	public function getApiDBGateway()
	{
		$instanceRootDir = app_path();

		$rawHost = static::getCurrentHost();
		$comps = parse_url($rawHost);

		if (isset($comps['host']) && isset($comps['port'])) {
			$host = $comps['host'];
		} else {
			$host = $rawHost;
		}

		$masterAppDb = str_replace(self::SAAS_API_INSTANCE.DIRECTORY_SEPARATOR.$host.DIRECTORY_SEPARATOR.self::SAAS_API_APP,
									 self::SAAS_API_ROOT_DIR.DIRECTORY_SEPARATOR.self::SAAS_API_BOOTSTRAP.DIRECTORY_SEPARATOR.self::SAAS_API_APP.self::SAAS_API_EXT, 
									 $instanceRootDir);

		if (is_file($masterAppDb)) {
			$db = require $masterAppDb;
			return $db;
		} else {
			throw new RuntimeException('Invalid instance path!');
		}
	}

	/**
	 * Get Alias
	 *
	 * @return string
	 */
	public function getAlias()
	{
		$cName = app_path('storage/cname');
		$rawHost = static::getCurrentHost();
		$comps = parse_url($rawHost);

		if (isset($comps['host']) && isset($comps['port'])) {
			$alias = $comps['host'];
		} else {
			$alias = $rawHost;
		}

		if (is_file($cName)) {
			$alias = file_get_contents($cName);
		}

		return 'http://'.$alias;
	}
}