<?php

return array(
	'defaults' => array(
		'driver' => 'beanstalkd',
		'queue' => array('default'),
		'host' => '127.0.0.1',
		'port' => 11300,
		'max_retry' => 5,
		'redis' => array(
			'prefix' => 'fuel',
			'db' => 0,
		)
	),

	'default_setup' => 'default',

	'setups' => array(
		'default' => array(),
	)
);
