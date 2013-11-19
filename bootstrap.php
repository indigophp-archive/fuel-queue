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

Autoloader::add_core_namespace('Queue');

Autoloader::add_classes(array(
	'Queue\\Queue'          => __DIR__ . '/classes/queue.php',
	'Queue\\QueueException' => __DIR__ . '/classes/queue.php',
));
