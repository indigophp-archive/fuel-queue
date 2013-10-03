<?php

namespace Fuel\Tasks;

class Resque
{
	/**
	 * Initialize Redis and Logger
	 */
	public function __construct()
	{
		// Basic setup options
		$redis = \Cli::option('redis', \Cli::option('r', \Config::get('queue.redis', '127.0.0.1:6379')));
		$prefix = \Cli::option('prefix', \Cli::option('p', \Config::get('queue.prefix')));
		$db = \Cli::option('db', \Cli::option('d', \Config::get('queue.db', 0)));

		// Initialize logger
		$this->logger = clone \Log::instance();
		$this->logger->popHandler();

		// Determine the filename
		$filepath = \Config::get('log_path') . date('Y/m') . DS;
		$filename = $filepath . date('d') . '.php';

		// Create the streamhandler, and activate the handler (copied from Log class)
		$stream = new \Monolog\Handler\StreamHandler($filename, \Monolog\Logger::NOTICE);
		$formatter = new \Monolog\Formatter\ResqueFormatter("%level_name% - %datetime% --> %message%".PHP_EOL, "Y-m-d H:i:s");
		$stream->setFormatter($formatter);
		$this->logger->pushHandler($stream);

		// Setup Redis connection
		\Resque::setBackend($redis, $db);

		if ( ! empty($prefix) && is_string($prefix))
		{
			\Cli::write("*** Prefix set to $prefix\n", "blue");
			$this->logger->log(\Monolog\Logger::INFO, 'Prefix set to {prefix}', array('prefix' => $prefix));
			\Resque_Redis::prefix($prefix);
		}
	}

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r resque
	 *
	 * @return string
	 */
	public function run()
	{
		$queue = explode(',', \Cli::option('queue', \Cli::option('q', 'default')));
		$blocking = \Cli::option('blocking', \Cli::option('b', \Config::get('queue.resque.blocking', false)));
		$interval = \Cli::option('interval', \Cli::option('i', \Config::get('queue.resque.interval', 5)));
		$count = \Cli::option('count', \Cli::option('c', \Config::get('queue.resque.count', 1)));
		$pidfile = \Cli::option('pidfile');

		if ( ! is_array($queue))
		{
			return \Cli::error("Set --queue or -q parameter containing the list of queues to work.\n", "red");
		}

		// Fork workers or start a single one
		if($count > 1)
		{
			for($i = 0; $i < $count; ++$i)
			{
				$pid = \Resque::fork();
				if($pid == -1)
				{
					return \Cli::error("Could not fork worker $i\n", "red");
					$logger->log(\Monolog\Logger::EMERGENCY, 'Could not fork worker {count}', array('count' => $i));
				}
				elseif( ! $pid)
				{
					$worker = new \Resque_Worker($queue);
					$worker->setLogger($this->logger);
					\Cli::write("*** Starting worker $worker\n", "green");
					$this->logger->log(\Monolog\Logger::NOTICE, 'Starting worker {worker}', array('worker' => (string)$worker));
					$worker->work((int) $interval, (bool) $blocking);
					break;
				}
			}
		}
		else
		{
			$worker = new \Resque_Worker($queue);
			$worker->logLevel = $log;

			if ($pidfile)
			{
				file_put_contents($pidfile, getmypid()) ||
					\Cli::write("*** Writing to PID file failed\n", "red");
			}

			\Cli::write("*** Starting worker $worker\n", "green");
			$worker->work((int) $interval, (bool) $blocking);
		}
	}

	public function stop($workers = null)
	{
		$workers = $this->workers($workers);
		$sig = \Cli::option('sig', \Cli::option('s', SIGQUIT));
		is_string($sig) && $sig = constant($sig);

		foreach ($workers as $worker) {
			if ($worker instanceof \Resque_Worker)
			{
				$pid = explode(':', $worker);
				\Cli::write("*** Stopping worker $worker\n", "red");
				$this->logger->log(\Monolog\Logger::NOTICE, 'Stopping worker {worker} (Signal: {signal})', array('worker' => (string)$worker, 'signal' => $sig));
				posix_kill($pid[1], $sig);
				$worker->pruneDeadWorkers();
			}
		}
	}


	public function status($workers = null)
	{
		$workers = $this->workers($workers);

		$count = 0;
		$jobs = array();

		foreach ($workers as $worker)
		{
			$job = $worker->job();

			if (! empty($job))
			{
				$job['worker'] = (string)$worker;
				$jobs[] = $job;
				$count++;
			}

			\Cli::write($worker, 'green');
			\Cli::write("\t* Scheduled: " . $worker->getStat('scheduled'), 'blue');
			\Cli::write("\t* Enqueued: " . $worker->getStat('enqueued'), 'blue');
			\Cli::write("\t* Processed: " . $worker->getStat('processed'), 'green');
			\Cli::write("\t* Finished: " . $worker->getStat('finished'), 'green');
			\Cli::write("\t* Failed: " . $worker->getStat('failed'), "red");
		}

		\Cli::write("\n$count of " . count($workers) . " Workers Working\n", "yellow");

		if ($count > 0)
		{
			\Cli::write("Token\t\t\t\t\tWorker\t\t\tQueue\t\tClass\t\tArgs", "blue");

			foreach ($jobs as $job)
			{
				\Cli::write($job['payload']['id'] . "\t" . $job["worker"] . "\t" . $job["queue"] . "\t\t" . $job['payload']['class'] . "\t" . json_encode($job['payload']['args']));
			}
		}
	}

	public function restart($workers = null)
	{
		$workers = $this->workers($workers);

		$blocking = \Cli::option('blocking', \Cli::option('b', \Config::get('queue.blocking', false)));
		$interval = \Cli::option('interval', \Cli::option('i', \Config::get('queue.interval', 5)));

		$restart = array();
		foreach ($workers as $worker)
		{
			if ($worker instanceof \Resque_Worker)
			{
				$pid = explode(':', $worker);
				$restart[] = array(
					'queues' => $worker->queues()
				);

				\Cli::write("*** Stopping worker $worker\n", "red");
				posix_kill($pid[1], SIGQUIT);
				$worker->pruneDeadWorkers();
			}
		}

		foreach ($restart as $w)
		{
			$pid = \Resque::fork();
			if($pid == -1)
			{
				return \Cli::error("Could not fork worker \n", "red");
			}
			// Child, start the worker
			elseif( ! $pid)
			{
				$worker = new \Resque_Worker($w['queues']);
				$worker->setLogger($this->logger);
				\Cli::write("*** Starting worker $worker\n", "green");
				$this->logger->log(\Monolog\Logger::NOTICE, 'Starting worker {worker}', array('worker' => (string)$worker));
				$worker->work((int) $interval, (bool) $blocking);
				break;
			}
		}
	}

	public function pause($workers = null)
	{
		$workers = $this->workers($workers);

		foreach ($workers as $worker) {
			if ($worker instanceof \Resque_Worker)
			{
				$pid = explode(':', $worker);
				\Cli::write("*** Pause worker " . $worker, 'green');
				posix_kill($pid[1], SIGUSR2);
			}
		}
	}

	public function unpause($workers = null)
	{
		$workers = $this->workers($workers);

		foreach ($workers as $worker) {
			if ($worker instanceof \Resque_Worker)
			{
				$pid = explode(':', $worker);
				\Cli::write("*** Unpause worker " . $worker, 'green');
				posix_kill($pid[1], SIGCONT);
			}
		}
	}

	public function failed()
	{
		$failed = \Resque::redis()->lrange('failed', 1, -1);

		foreach ($failed as $job) {
			$job = json_decode($job);
			var_dump($job); exit;
		}
	}

	private function workers($workers = null)
	{
		if (is_null($workers)) {
			$workers = \Resque_Worker::all();
		}
		else
		{
			if (\Resque_Worker::exists($workers))
			{
				$workers = array(\Resque_Worker::find($workers));
			}
			else
			{
				\Cli::write("*** Worker $workers does not exists", "red");
				exit(0);
			}
		}

		if (empty($workers))
		{
			\Cli::write("*** No workers running", "red");
			exit(0);
		}
		else
		{
			return $workers;
		}
	}
}
/* End of file tasks/resque.php */
