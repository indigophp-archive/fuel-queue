<?php

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
	'default_setup' => 'default',

	/**
	 * Setup groups
	 */
	'setups' => array(
		'default' => array(),
	)
);
