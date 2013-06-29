<?php

namespace Queue;

class QueueException extends \FuelException {}

class Queue
{

	/**
	 * loaded instance
	 */
	protected static $_instance = null;

	/**
	 * array of loaded instances
	 */
	protected static $_instances = array();

	/**
	 * Default config
	 * @var array
	 */
	protected static $_defaults = array(
		'driver'   => 'resque',
		'redis'    => '127.0.0.1:6379',
		'workers'  => 2,
		'interval' => 5,
		'blocking' => false,
		'prefix'   => 'fuel',
		'db'       => 0
	);

	/**
	 * Queue driver forge.
	 *
	 * @param	string			$queue		Queue name
	 * @param	mixed			$config		Extra config array or the driver
	 * @return  Queue instance
	 */
	public static function forge($queue = 'default', $config = array())
	{
		! is_array($config) && $config = array('driver' => $config);

		$config = \Arr::merge(static::$_defaults, \Config::get('queue', array()), $config);

		$class = '\Queue\Queue_' . ucfirst($config['driver']);

		if( ! class_exists($class, true))
		{
			throw new \FuelException('Could not find Queue driver: ' . $config['driver']);
		}

		$driver = new $class($queue, $config);

		static::$_instances[$queue] = $driver;

		return $driver;
	}

	/**
	 * Return a specific driver, or the default instance (is created if necessary)
	 *
	 * @param   string  $instance
	 * @return  Queue instance
	 */
	public static function instance($instance = null)
	{
		if ($instance !== null)
		{
			if ( ! array_key_exists($instance, static::$_instances))
			{
				return false;
			}

			return static::$_instances[$instance];
		}

		if (static::$_instance === null)
		{
			static::$_instance = static::forge();
		}

		return static::$_instance;
	}

	public static function enqueue($queue = null, $job, $args = null)
	{
		static::instance($queue)->enqueue($job, $args);
	}

}