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
	 * Default settings
	 */
	'defaults' => array(

		/**
		 * Queue driver (direct, beanstalkd, resque)
		 */
		'driver' => 'beanstalkd',

		/**
		 * Default queues
		 */
		'queue' => array('default'),

		/**
		 * Only enable queues given in the setup
		 */
		'restrict_queue' => false,

		/**
		 * Queue host
		 */
		'host' => '127.0.0.1',

		/**
		 * Queue port
		 */
		'port' => 11300,

		/**
		 * Max retry of a job
		 */
		'max_retry' => 5,

		/**
		 * Redis config
		 */
		'redis' => array(
			'prefix' => 'fuel',
			'db' => 0,
		)
	),

	/**
	 * Default setup group
	 */
	'default' => 'default',

	/**
	 * Setup groups
	 */
	'setups' => array(
		'default' => array(),
	)
);
