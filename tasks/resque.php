<?php

namespace Fuel\Tasks;

class Resque
{

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r resque
	 *
	 * @return string
	 */
	public static function run($args = NULL)
	{
		$queue = \Cli::option('queue', \Cli::option('q', '*'));
		$redis = \Cli::option('redis', \Cli::option('r'));
		$db = \Cli::option('db', \Cli::option('d', 0));
		$log = \Cli::option('log', \Cli::option('l'));
		$verbose = \Cli::option('verbose', \Cli::option('v'));
		$vverbose = \Cli::option('vverbose', \Cli::option('vv'));
		$blocking = \Cli::option('blocking', \Cli::option('b', false));
		$interval = \Cli::option('interval', \Cli::option('i', 5));
		$count = \Cli::option('count', \Cli::option('c', 1));
		$prefix = \Cli::option('prefix', \Cli::option('p'));
		$pidfile = \Cli::option('pidfile');

		if (empty($queue) || ! is_string($queue))
		{
			return \Cli::error(\Cli::color("Set --queue or -q parameter containing the list of queues to work.\n", "red"));
		}

		if( ! empty($redis))
		{
			\Resque::setBackend($redis, $db);
		}

		$logLevel = 0;
		if( ! empty($log) || ! empty($verbose))
		{
			$logLevel = \Resque_Worker::LOG_NORMAL;
		}
		elseif( ! empty($vverbose))
		{
			$logLevel = \Resque_Worker::LOG_VERBOSE;
		}

		if ( ! empty($prefix) && is_string($prefix))
		{
			\Cli::write(\Cli::color("*** Prefix set to $prefix\n", "blue"));
		}

		if($count > 1)
		{
			for($i = 0; $i < $count; ++$i)
			{
				$pid = \Resque::fork();
				if($pid == -1)
				{
					return \Cli::error(\Cli::color("Could not fork worker $i\n", "red"));
				}
				// Child, start the worker
				elseif( ! $pid)
				{
					$queues = explode(',', $queue);
					$worker = new \Resque_Worker($queues);
					$worker->logLevel = $logLevel;
					\Cli::write(\Cli::color("*** Starting worker $worker\n", "green"));
					$worker->work((int) $interval, (bool) $blocking);
					break;
				}
			}
		}
		// Start a single worker
		else
		{
			$queues = explode(',', $queue);
			$worker = new \Resque_Worker($queues);
			$worker->logLevel = $logLevel;

			if ($pidfile)
			{
				file_put_contents($pidfile, getmypid()) ||
					\Cli::write(\Cli::color("*** Writing to PID file failed\n", "red"));
			}

			\Cli::write(\Cli::color("*** Starting worker $worker\n", "green"));
			$worker->work((int) $interval, (bool) $blocking);
		}
	}
}
/* End of file tasks/resque.php */
