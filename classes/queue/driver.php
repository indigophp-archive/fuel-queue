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
	final public function __construct($queue, array $config = array())
	{
		$this->queue = $queue;
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
	 * Init function instead of the __construct
	 * @return void
	 */
	abstract protected function _init();

	/**
	 * Push a job to the queue
	 * @param  string $job   Job name
	 * @param  array $args  Optional array of arguments
	 * @return string        Job token
	 */
	public function push($job, array $args = array())
	{
		if( ! class_exists($job, true))
		{
			throw new \QueueException('Could not find Job: ' . $job);
		}

		return $this->_push($job, $args);
	}

	/**
	 * Push a job to the queue
	 * @param  string $job   Job name
	 * @param  array $args  Optional array of arguments
	 * @return string        Job token
	 */
	abstract protected function _push($job, array $args = array());
}
