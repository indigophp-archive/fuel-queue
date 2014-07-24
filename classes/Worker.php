<?php

/*
 * This file is part of the Fuel Queue package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Fuel;

use Indigo\Queue\Worker as WorkerClass;
use Psr\Log\LoggerInterface;

/**
 * Worker Facade class
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Worker extends Queue
{
	/**
	 * {@inheritdoc}
	 *
	 * @param string $instance
	 *
	 * @return Indigo\Queue\Worker
	 */
	public static function forge($instance = 'default')
	{
		$worker = new WorkerClass($instance, static::resolveConnector($instance));

		if ($logger = \Config::get('queue.logger') and $logger instanceof LoggerInterface) {
			$worker->setLogger($logger);
		}

		return static::newInstance($instance, $worker);
	}
}
