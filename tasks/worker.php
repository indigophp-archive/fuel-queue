<?php

/*
 * This file is part of the Fuel Queue package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(ticks = 1);

namespace Fuel\Tasks;

/**
 * Worker Task
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Worker
{
	public function __construct()
	{
		$shutdown = function() {
			$quiet = \Cli::option('quiet', \Cli::option('q', false));
			$quiet or \Cli::write(PHP_EOL . 'Worker ' . getmypid() . ' is stopping', 'red');
		};

		try
		{
			pcntl_signal(SIGINT, $shutdown);
			pcntl_signal(SIGINT, function () { exit; });
		}
		catch (\PhpErrorException $e)
		{
		}

		\Event::register('shutdown', $shutdown);
	}

	/**
	 * Listen to a queue
	 *
	 * @param mixed $queue
	 */
	public function run($queue = 'default', $connector = null)
	{
		$worker = \Worker::forge($queue, $connector);

		$interval = \Cli::option('interval', \Cli::option('i', 5));

		$worker->listen($interval);
	}

	/**
	 * Process a job from a queue
	 *
	 * @param mixed $queue
	 */
	public function work($queue = 'default', $connector = null)
	{
		$worker = \Worker::forge($queue, $connector);

		$worker->work();
	}
}
