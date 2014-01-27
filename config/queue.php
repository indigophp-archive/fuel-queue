<?php
/**
 * Fuel Queue
 *
 * @package 	Fuel
 * @subpackage	Queue
 * @version		2.0
 * @author 		Márk Sági-Kazár <mark.sagikazar@gmail.com>
 * @license 	MIT License
 * @link 		https://indigophp.com
 */

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */

return array(
    /**
     * Predefined queue instances
     */
    'queue' => array(),

    /**
     * Default connector
     */
    'default' => 'default',

    /**
     * Connector instances
     */
    'connector' => array(),

    /**
     * Logger instance for worker
     * Must evaluate to Psr\Log\LoggerInterface
     */
    'logger' => \Log::instance(),
);
