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
	public static function forge($setup = null, $config = array())
	{

		empty($setup) and $setup = \Config::get('queue.default_setup', 'default');
		is_string($setup) and $setup = \Config::get('queue.setups.'.$setup, array());

		if ( ! empty($config) and ! is_array($config))
		{
			$config = array('driver' => $config);
		}

		$setup = \Arr::merge(static::$_defaults, $setup);
		$config = \Arr::merge($setup, $config);

		! is_array($config['queue']) && $config['queue'] = explode(',', $config['queue']);

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
