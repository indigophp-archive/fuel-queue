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

class Worker
{
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
     * @return Worker
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

        $instance = new \Indigo\Queue\Worker($queue, $connector);
        $instance->setLogger(\Config::get('queue.logger', \Log::instance()));

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
