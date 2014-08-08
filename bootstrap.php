<?php

/*
 * This file is part of the Fuel Queue package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Autoloader::add_core_namespace('Queue');

Autoloader::add_classes(array(
	'Queue\\Queue' => __DIR__ . '/classes/queue.php',
	'Queue\\Worker' => __DIR__ . '/classes/worker.php',
));
