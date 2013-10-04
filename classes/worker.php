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

class WorkerException extends \FuelException {}

class Worker
{

	/**
	 * Default config
	 * @var array
	 */
	protected static $_defaults;

	/**
	 * Worker driver forge.
	 *
	 * @param	mixed			$setup		Setup name
	 * @param	mixed			$config		Extra config array or the driver
	 * @return  Worker instance
	 */
	public static function forge($config = array())
	{
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

		is_array($config['queue']) or $config['queue'] = explode(',', $config['queue']);

		$class = '\\Queue\\Worker_' . ucfirst(strtolower($config['driver']));

		if( ! class_exists($class, true))
		{
			throw new \WorkerException('Could not find Worker driver: ' . $config['driver']);
		}

		$driver = new $class($config);

		return $driver;
	}

	/**
	 * Init, config loading.
	 */
	public static function _init()
	{
		static::$_defaults = \Config::get('queue.defaults');
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
