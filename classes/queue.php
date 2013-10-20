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

use Phresque\Queue\QueueInterface;
use Phresque\Queue\DirectQueue;
use Psr\Log\LoggerInterface;

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
	protected static $_defaults = array(
		'driver' => 'direct',
		'auto'   => false,
		'connection' => array(
			'host' => '127.0.0.1',
			'port' => 11300,
		)
	);

	/**
	 * Queue driver instance.
	 *
	 * @param	mixed		$queue		Queue name
	 * @param	mixed		$config		Extra config array
	 * @return	QueueInterface
	 */
	public static function instance($queue = 'default', array $config = array())
	{
		// Set instance if QueueInterface passed
		// or return existing instance
		// or instantiate class
		if ($queue instanceof QueueInterface)
		{
			$driver = $queue;
			$queue  = $driver->getQueue();
		}
		elseif (array_key_exists($queue, static::$_instances))
		{
			return static::$_instances[$queue];
		}
		else
		{
			// Merge configs and get driver
			$config = \Arr::merge(static::$_defaults, \Config::get('queue.queues.' . $queue, array()), $config);
			$driver = \Arr::get($config, 'driver', 'direct');

			if ( ! $driver instanceof QueueInterface)
			{
				// Check driver availability
				class_exists($driver) or $driver = '\\Phresque\\Queue\\' . ucfirst(strtolower($driver)) . 'Queue';

				if( ! class_exists($driver))
				{
					throw new \QueueException('Could not find Queue driver: ' . $driver);
				}

				// Instantiate queue
				$driver = new $driver($queue, \Arr::get($config, 'connection', array()));
			}
		}

		// Fallback to direct driver
		$driver->isAvailable() or $driver = new DirectQueue();

		// Set logger instance
		$driver->setLogger(\Arr::get($config, 'logger', \Log::instance()));

		// Return queue instance
		return static::$_instances[$queue] = $driver;
	}

	/**
	 * Init, config loading.
	 */
	public static function _init()
	{
		// Load config and defaults
		\Config::load('queue', true);
		static::$_defaults = \Arr::merge(static::$_defaults, \Config::get('queue.defaults', array()));

		// Get defined queues for autoload
		$auto = \Config::get('queue.queues', array());

		foreach ($auto as $queue => $config)
		{
			// Autoload some queues
			\Arr::get($config, 'auto', false) === true and static::instance($queue);
		}
	}

	/**
	 * Push a job from static interface
	 *
	 * @param	mixed	$queue	Queue name or array of queue name and config or QueueInterface
	 * @param	string	$job	Job name
	 * @param	array	$data	Optional array of arguments
	 * @return	string			Job token
	 */
	public static function push($queue, $job, $data = array())
	{
		// Get args that will be pushed to the queue drivers
		$args = func_get_args() and array_shift($args);

		// No QueueInterface passed
		if ( ! $queue instanceof QueueInterface)
		{
			// Queue is an array, so it also contains config
			is_array($queue) ? list($queue, $config) = $queue : $config = array();
			$queue = static::instance($queue, $config);
		}

		// Call instance
		$callable = array($queue, 'push');
		return call_user_func_array($callable, $args);
	}

	/**
	 * Push a delayed job from static interface
	 *
	 * @param	mixed	$queue	Queue name or array of queue name and config or QueueInterface
	 * @param	integer	$delay	Seconds the job should be delayed
	 * @param	string	$job	Job name
	 * @param	array	$data	Optional array of arguments
	 * @return	string			Job token
	 */
	public static function delayed($queue, $delay, $job, $data = array())
	{
		// Get args that will be pushed to the queue drivers
		$args = func_get_args() and array_shift($args);

		// No QueueInterface passed
		if ( ! $queue instanceof QueueInterface)
		{
			// Queue is an array, so it also contains config
			is_array($queue) ? list($queue, $config) = $queue : $config = array();
			$queue = static::instance($queue, $config);
		}

		// Call instance
		$callable = array($queue, 'delayed');
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
