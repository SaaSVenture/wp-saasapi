<?php namespace Saas\Sdk;

/**
 * API Credential Object
 *
 * @author Taufan Adhitya <toopay@taufanaditya.com>
 * @package saas/sdk
 */

class Credential
{
	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var string
	 */
	private $secret;

	/**
	 * Constructor
	 *
	 * @param string
	 * @param string
	 */
	public function __construct($key, $secret)
	{
		$this->key = $key;
		$this->secret = $secret;
	}

	/**
	 * Key getter
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Secret getter
	 *
	 * @param string
	 */
	public function getSecret()
	{
		return $this->secret;
	}
}