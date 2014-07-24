<?php

/*
 * This file is part of the Fuel Queue package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
	 * Connector instances
	 */
	'connector' => array(),

	/**
	 * Logger instance for worker
	 * Must evaluate to Psr\Log\LoggerInterface
	 */
	'logger' => \Log::instance(),
);
