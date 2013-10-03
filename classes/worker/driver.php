<?php
/**
 * Fuel Queue
 *
 * @package 	Fuel
 * @subpackage	Queue
 * @version		1.0
 * @author 		Márk Sági-Kazár <mark.sagikazar@gmail.com>
 * @license 	MIT License
 * @link 		https://github.com/indigo-soft
 */

namespace Queue;

abstract class Worker_Driver
{

	/**
	 * Queue identifier(s)
	 *
	 * @var array
	 */
	protected $queue;

	/**
	* Driver config
	*
	* @var array
	*/
	protected $config = array();

	/**
	* Driver constructor
	*
	* @param array $config driver config
	*/
	final public function __construct(array $config = array())
	{
		$this->config = $config;
		$this->_init();
	}

	/**
	* Get a driver config setting
	*
	* @param	string|null		$key		Config key
	* @param	mixed			$default	Default value
	* @return	mixed						Config setting value or the whole config array
	*/
	public function get_config($key = null, $default = null)
	{
		return is_null($key) ? $this->config : \Arr::get($this->config, $key, $default);
	}

	/**
	* Set a driver config setting
	*
	* @param	string|array	$key		Config key or array of key-value pairs
	* @param	mixed			$value		New config value
	* @return	$this						$this for chaining
	*/
	public function set_config($key, $value = null)
	{
		if (is_array($key))
		{
			$this->config = \Arr::merge($this->config, $key);
		}
		else
		{
			\Arr::set($this->config, $key, $value);
		}

		return $this;
	}

	/**
	 * Init function
	 */
	abstract protected function _init();

	/**
	 * Initialize worker
	 */
	abstract public function work();
}
