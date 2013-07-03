<?php

namespace Queue;

abstract class Queue_Driver
{

	/**
	 * Queue identifier
	 * @var string
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
	public function __construct($queue, array $config = array())
	{
		$this->queue = $queue;
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
	 * Enqueue a job
	 * @param  string $job   Job name
	 * @param  array $args  Optional array of arguments
	 * @return string        Job token
	 */
	abstract public function enqueue($job, array $args = array());
}
