<?php namespace Saas\Sdk;

/**
 * Resource Object Representation (POPO - Plain PHP Object)
 *
 * @author Taufan Adhitya <toopay@taufanaditya.com>
 * @package saas/sdk
 */

use ArrayAccess;

class ResourceObject implements ArrayAccess
{
	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * Constructor
	 *
	 * @param mixed 
	 */
	public function __construct($resource = null)
	{
		if (!empty($resource)) {
			$this->data = (array) $resource;
		}
	}

	/**
	 * @{inheritDoc}
	 */
	public function offsetExists($offset)
	{
		return isset($this->data[$offset]);
	}

	/**
	 * @{inheritDoc}
	 */
	public function offsetGet($offset)
	{
		return $this->offsetExists($offset) ? $this->data[$offset] : null;
	}

	/**
	 * @{inheritDoc}
	 */
	public function offsetSet($offset, $value)
	{
		$this->data[$offset] = $value;
	}

	/**
	 * @{inheritDoc}
	 */
	public function offsetUnset($offset)
	{
		if ($this->offsetExists($offset)) unset($this->data[$offset]);
	}

	/**
	 * Global getter overider
	 *
	 * @param string
	 * @return mixed
	 */
	public function __get($name)
	{
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}

	/**
	 * Global setter overider
	 *
	 * @param string
	 * @param mixed
	 */
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

	/**
	 * Return array representation
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->data;
	}
}