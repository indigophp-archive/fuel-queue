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
		// Return instance if exists
		if (array_key_exists($queue, static::$_instances))
		{
			return static::$_instances[$queue];
		}

		// When a string was passed it's just the setup
		if (is_string($config))
		{
			$setup = $config;
			$config = array();
		}

		// Get setup if not set, get it from config
		empty($connection) and $connection = \Arr::get($config, 'connection', \Config::get('queue.default', 'default'));

		// Merge config and get driver
		$config  = \Arr::merge(static::$_defaults, \Config::get('queue.connections.' . $connection, array()), $config);
		$driver  = \Arr::get($config, 'driver');

		// Check driver availability
		$class = '\\Phresque\\Queue\\' . ucfirst(strtolower($driver)) . 'Queue';

		if( ! class_exists($class, true))
		{
			throw new \QueueException('Could not find Queue driver: ' . $driver);
		}

		// Instantiate queue
		$driver = new $class($queue, $config['connection']);

		// Fallback to direct driver
		$driver->isAvailable() or $driver = new \Phresque\Queue\DirectQueue();

		// Set logger instance
		$driver->setLogger(\Arr::get($config, 'logger', \Log::instance()));

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
	 * @param	mixed	$queue	queue name or array of queue name and config
	 * @param	string	$job	Job name
	 * @param	array	$args	Optional array of arguments
	 * @return	string			Job token
	 */
	public static function push($queue, $job, $data = array())
	{
		// Get args that will be pushed to the queue drivers
		$args = func_get_args() and array_shift($args);

		// Queue is an array, so it also contains config
		is_array($queue) ? list($queue, $config) = $queue : $config = array();

		// Call instance
		$callable = array(static::instance($queue, $config), 'push');
		return call_user_func_array($callable, $args);
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
