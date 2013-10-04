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

class QueueException extends \FuelException {}

class Queue
{

	/**
	 * Loaded instances
	 *
	 * @var array
	 */
	protected static $_instances = array();

	/**
	 * Default config
	 *
	 * @var array
	 */
	protected static $_defaults;

	/**
	 * Queue driver instance.
	 *
	 * @param	string		$queue		Queue name
	 * @param	mixed		$setup		Setup name or extra config
	 * @param	mixed		$config		Extra config array
	 * @return	object		new Queue_Driver
	 */
	public static function instance($queue = 'default', $config = array())
	{
		if (array_key_exists($queue, static::$_instances))
		{
			return static::$_instances[$instance];
		}

		// When a string was passed it's just the setup
		if (is_string($config))
		{
			$setup = $config;
			$config = array();
		}

		// Get setup if not set, get it from config
		empty($setup) and $setup = \Arr::get($config, 'setup', \Config::get('queue.default', 'default'));

		// Merge config and get driver
		$config  = \Arr::merge(static::$_defaults, \Config::get('queue.setups.' . $setup, array()), $config);
		$driver  = \Arr::get($config, 'driver');

		$class = '\\Queue\\Queue_' . ucfirst(strtolower($driver));

		if( ! class_exists($class, true))
		{
			throw new \QueueException('Could not find Queue driver: ' . $driver);
		}

		// Restrict queue passed to be in config
		if((\Arr::get($config, 'restrict_queue') === true and ! in_array($queue, \Arr::get($config, 'queue'))) and ! in_array('*', $config['queue']))
		{
			throw new \QueueException($queue . ' is not part of this setup.');
		}

		$driver = new $class($queue, $config);

		static::$_instances[$queue] = $driver;

		return static::$_instances[$queue];
	}

	/**
	 * Init, config loading.
	 */
	public static function _init()
	{
		static::$_defaults = \Config::get('queue.defaults');
	}

	/**
	 * Push a job from static interface
	 *
	 * @param	string	$job	Job name
	 * @param	array	$args	Optional array of arguments
	 * @param	string	$queue	Optional queue name
	 * @return	string			Job token
	 */
	public static function push($job, array $args = array(), $queue = 'default')
	{
		return static::instance($queue)->enqueue($job, $args);
	}

	/**
	 * class constructor
	 *
	 * @param	void
	 * @access	private
	 * @return	void
	 */
	final private function __construct() {}

}
