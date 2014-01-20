<?php
/**
 * Fuel Queue
 *
 * @package     Fuel
 * @subpackage  Queue
 * @version     2.0
 * @author      Márk Sági-Kazár <mark.sagikazar@gmail.com>
 * @license     MIT License
 * @link        https://indigophp.com
 */

namespace Queue;

use Indigo\Queue\Connector\ConnectorInterface;
use Indigo\Queue\Queue;

class Queue
{
    /**
     * Array of ConnectorInterface instances
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
     * @param  string             $queue    Queue name
     * @param  string             $instance Instance name
     * @param  ConnectorInterface $object   Object instance if not exists
     * @return Queue
     */
    public static function forge($queue, $instance = null, ConnectorInterface $object = null)
    {
        is_null($instance) and $instance = \Config::get('queue.default');
        is_null($object) and $object = \Config::get('queue.instances.' . $instance);

        if (is_null($object) or ! $object instanceof ConnectorInterface)
        {
            throw new \InvalidArgumentException('There is no valid Connector object');
        }

        $object = new Queue($queue, $object);

        return static::$_instances[$instance] = $object;
    }

    /**
     * Return or forge an instance
     *
     * @param  string $instance Instance name
     * @return ConnectorInterface
     */
    public static function instance($instance = null)
    {
        if (array_key_exists($instance, static::$_instances))
        {
            $instance = static::$_instances[$instance];
        }
        else
        {
            $instance = static::forge($instance);
        }

        return $instance;
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
