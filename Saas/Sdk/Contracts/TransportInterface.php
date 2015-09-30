<?php namespace Saas\Sdk\Contracts;

/**
 * Transport Layer Interface
 *
 * @author Taufan Adhitya <toopay@taufanaditya.com>
 * @package saas/sdk
 */

interface TransportInterface 
{
	const SAAS_API_APP = 'app';
	const SAAS_API_BOOTSTRAP = 'bootstrap';
	const SAAS_API_INSTANCE = 'instances';
	const SAAS_API_ROOT_DIR = 'app-saasapi';
	const SAAS_API_DEFAULT_ROOT = 'saasapi.com';
	const SAAS_API_DEVELOPER_ROOT = 'developer.saasapi.com';
	const SAAS_API_EXT = '.php';
	const SAAS_API_DOMAIN_SEPARATOR = '.';

	/**
	 * Get the app's that own this API subscription
	 *
	 * @return Saas\Sdk\ResourceObject
	 */
	public function getOwnerApp();

	/**
	 * Get the app's identity
	 *
	 * @return Saas\Sdk\ResourceObject
	 */
	public function getOwnerAppIdentity();

	/**
	 * Get all users
	 *
	 * @return Saas\Sdk\ResourceCollection
	 */
	public function getUsers();

	/**
	 * Get all companies
	 *
	 * @return Saas\Sdk\ResourceCollection
	 */
	public function getCompanies();

	/**
	 * Get user resource
	 *
	 * @param int
	 * @return Saas\Sdk\ResourceObject
	 */
	public function getUser($id);

	/**
	 * Get company resource
	 *
	 * @param int
	 * @return Saas\Sdk\ResourceObject
	 */
	public function getCompany($id);

	/**
	 * Switch user company
	 *
	 * @param int
	 * @param int
	 * @return Saas\Sdk\ResourceObject
	 */
	public function switchCompany($userId, $brandId);

	/**
	 * Get companies by user id
	 *
	 * @param int
	 * @param bool
	 * @return Saas\Sdk\ResourceCollection
	 */
	public function getCompaniesByUser($userId, $onlyActive = false);

	/**
	 * Get current company subscription
	 *
	 * @param int
	 * @return Saas\Sdk\ResourceObject
	 */
	public function getCurrentSubscription($companyId);

	/**
	 * Clear specific session id
	 *
	 * @param string Session ID
	 * @return void
	 */
	public function clearSession($sessionId);

	/**
	 * Get available plans
	 *
	 * @return Saas\Sdk\ResourceCollection
	 */
	public function getPlans();

	/**
	 * Get user message stats
	 * 
	 * @param int
	 * @return Saas\Sdk\ResourceObject
	 */
	public function getMessageStats($userId);

	/**
	 * Get inbox by user
	 *
	 * @param string user id
	 * @param int    Total message per page
	 * @param int    Page
	 * @return Saas\Sdk\ResourceCollection
	 */
	public function getInboxByUser($userId, $perPage, $page);

	/**
	 * Get outbox by user
	 *
	 * @param string user id
	 * @param int    Total message per page
	 * @param int    Page
	 * @return Saas\Sdk\ResourceCollection
	 */
	public function getOutboxByUser($userId, $perPage, $page);

	/**
	 * Send a message
	 *
	 * @param string Sender id
	 * @param string Receiver id
	 * @param string Message subject
	 * @param string Message body
	 * @return Saas\Sdk\ResourceObject
	 */
	public function sendUserMessage($fromId, $toId, $subject, $message);

	/**
	 * Get a message
	 *
	 * @param string 	Message id
	 * @param boolean 	Set read status flag
	 */
	public function getMessage($id, $markAsRead);

	/**
	 * Delete message
	 */
	public function deleteMessage($id);

	/**
	 * Perform message batch operation
	 *
	 * @param string operation
	 * @param array  Message ids
	 */
	public function performBatchMessages($operation, $ids);

}