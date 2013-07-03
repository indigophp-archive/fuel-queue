<?php

return array(
	'driver' => 'resque',
	'redis' => \Config::get('db.redis.resque.host', '127.0.0.1') . ':' . \Config::get('db.redis.resque.port', 6379),
	'workers' => 3,
	'interval' => 5,
	'blocking' => false,
	'prefix' => 'fuel',
	'db' => 0
);
