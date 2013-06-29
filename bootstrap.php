<?php

Autoloader::add_core_namespace('Queue');

Autoloader::add_classes(array(
	'Queue\\Queue' => __DIR__ . '/classes/queue.php',
	'Queue\\QueueException' => __DIR__ . '/classes/queue.php',

	'Queue\Queue_Driver' => __DIR__ . '/classes/queue/driver.php',
	'Fuel\\Core\\Job' => __DIR__ . '/classes/job.php',

	'Queue\\Queue_Resque' => __DIR__ . '/classes/queue/resque.php',
	'Queue\\Queue_Direct' => __DIR__ . '/classes/queue/direct.php',
));