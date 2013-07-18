<?php

namespace Queue;

class WorkerException extends \FuelException {}

class Worker
{

	/**
	 * Default config
	 * @var array
	 */
	protected static $_defaults = array(
		'driver'   => 'resque'
	);

	/**
	 * Worker driver forge.
	 *
	 * @param	mixed			$queue		Queue name
	 * @param	mixed			$custom		Extra config array or the driver
	 * @return  Worker instance
	 */
	public static function forge($queue = 'default', $custom = array())
	{
		if ( ! empty($custom) and ! is_array($custom))
		{
			$custom = array('driver' => $custom);
		}

		! is_array($queue) && $queue = explode(',', $queue);

		$config = \Arr::merge(static::$_defaults, \Config::get('queue', array()), $custom);

		if ( ! empty($config['driver']))
		{
			$config = \Arr::merge($config, \Config::get('queue.' . $config['driver'], array()), $custom);
		}
		else
		{
			throw new \WorkerException('No Worker driver given or no default Worker driver set.');
		}

		$class = '\\Queue\\Worker_' . ucfirst(strtolower($config['driver']));

		if( ! class_exists($class, true))
		{
			throw new \WorkerException('Could not find Worker driver: ' . $config['driver']);
		}

		$driver = new $class($queue, $config);

		return $driver;
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
