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

abstract class Queue_Driver
{

	/**
	 * Queue name
	 *
	 * @var string
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
	* @param	string	$queue		Queue
	* @param	array	$config		Driver config
	*/
	public function __construct($queue, array $config = array())
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
	 * Init function
	 */
	abstract protected function _init();

	/**
	 * Push a job to the queue
	 *
	 * @param	string	$job	Job name
	 * @param	array	$args	Optional array of arguments
	 * @return	string			Job token
	 */
	public function push($job, array $args = array())
	{
		if( ! class_exists($job, true))
		{
			throw new \QueueException('Could not find Job: ' . $job);
		}
		if (func_num_args() > 2)
		{
			call_user_func_array(array($this, '_push'), func_get_args());
		}
		else
		{
			return $this->_push($job, $args);
		}
	}

	/**
	 * Push a job to the queue
	 *
	 * @param	string	$job	Job name
	 * @param 	array	$args	Optional array of arguments
	 * @return	string			Job token
	 */
	abstract protected function _push($job, array $args = array());
}
