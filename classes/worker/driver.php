<?php

namespace Queue;

abstract class Worker_Driver
{

	/**
	 * Queue identifier(s)
	 * @var array
	 */
	protected $queue;

	/**
	* Driver config
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
	* Get a driver config setting.
	*
	* @param string $key the config key
	* @param mixed  $default the default value
	* @return mixed the config setting value
	*/
	public function get_config($key, $default = null)
	{
		return \Arr::get($this->config, $key, $default);
	}

	/**
	* Set a driver config setting.
	*
	* @param string $key the config key
	* @param mixed $value the new config value
	* @return object $this for chaining
	*/
	public function set_config($key, $value)
	{
		\Arr::set($this->config, $key, $value);

		return $this;
	}

	/**
	 * Init function instead of the __construct
	 * @return void
	 */
	abstract protected function _init();

	/**
	 * Initialize worker
	 */
	abstract public function work();
}
