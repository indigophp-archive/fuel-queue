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

Autoloader::add_core_namespace('Queue');

Autoloader::add_classes(array(
    'Queue\\Queue' => __DIR__ . '/classes/queue.php',
    'Queue\\Worker' => __DIR__ . '/classes/worker.php',
));
