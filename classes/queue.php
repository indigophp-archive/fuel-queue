<?php
/**
 * Fuel Queue
 *
 * @package     Fuel
 * @subpackage  Queue
 * @version     2.0
 * @author      Márk Sági-Kazár <mark.sagikazar@gmail.com>
 * @license     MIT License
 * @copyright   2013 - 2014 Indigo Development Team
 * @link        https://indigophp.com
 */

namespace Queue;

use Indigo\Queue\Connector\ConnectorInterface;

class Queue
{
	/**
	 * Array of Queue instances
	 *
	 * @var array
	 */
	protected static $_instances = array();

	/**
	 * Init
	 */
	public static function _init()
	{
		\Config::load('queue', true);
	}

	/**
	 * Forge and return new instance
	 *
	 * @param  string $queue     Queue name
	 * @param  string $connector Connector name or instance
	 * @return Queue
	 */
	public static function forge($queue, $connector = null)
	{
		if (is_null($connector))
		{
			$connector = \Config::get('queue.queue.' . $queue);

			// Queue is not found or set to null: default
			is_null($connector) and $connector = \Config::get('queue.default');
		}

		is_string($connector) and $connector = \Config::get('queue.connector.' . $connector);

		if ( ! $connector instanceof ConnectorInterface)
		{
			throw new \InvalidArgumentException('There is no valid Connector');
		}

		$instance = new \Indigo\Queue\Queue($queue, $connector);
		$instance->setLogger(\Log::instance());

		return static::$_instances[$queue] = $instance;
	}

	/**
	 * Return a queue instance
	 *
	 * @param  string $queue Queue name
	 * @return Queue
	 */
	public static function instance($queue = null)
	{
		if (array_key_exists($queue, static::$_instances))
		{
			$queue = static::$_instances[$queue];
		}
		else
		{
			$queue = static::forge($queue);
		}

		return $queue;
	}

	/**
	 * class constructor
	 *
	 * @param   void
	 * @access  private
	 * @return  void
	 */
	final private function __construct() {}
}
