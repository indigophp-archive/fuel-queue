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
	public static function run()
	{
		$queue = \Cli::option('queue', \Cli::option('q', 'default'));
		$log = \Cli::option('log', \Cli::option('l'));
		$verbose = \Cli::option('verbose', \Cli::option('v'));
		$vverbose = \Cli::option('vverbose', \Cli::option('vv'));
		$blocking = \Cli::option('blocking', \Cli::option('b', \Config::get('queue.blocking', false)));
		$interval = \Cli::option('interval', \Cli::option('i', \Config::get('queue.interval', 5)));
		$count = \Cli::option('count', \Cli::option('c', \Config::get('queue.workers', 1)));
		$pidfile = \Cli::option('pidfile');

		if (empty($queue) || ! is_string($queue))
		{
			return \Cli::error("Set --queue or -q parameter containing the list of queues to work.\n", "red");
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

		\Event::instance('queue')->trigger('resque_init');

		if($count > 1)
		{
			for($i = 0; $i < $count; ++$i)
			{
				$pid = \Resque::fork();
				if($pid == -1)
				{
					return \Cli::error("Could not fork worker $i\n", "red");
				}
				// Child, start the worker
				elseif( ! $pid)
				{
					$queues = explode(',', $queue);
					$worker = new \Resque_Worker($queues);
					$worker->logLevel = $logLevel;
					\Cli::write("*** Starting worker $worker\n", "green");
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
					\Cli::write("*** Writing to PID file failed\n", "red");
			}

			\Cli::write("*** Starting worker $worker\n", "green");
			$worker->work((int) $interval, (bool) $blocking);
		}
	}

	public function __construct()
	{
		$redis = \Cli::option('redis', \Cli::option('r', \Config::get('queue.redis', '127.0.0.1:6379')));
		$prefix = \Cli::option('prefix', \Cli::option('p', \Config::get('queue.prefix')));
		$db = \Cli::option('db', \Cli::option('d', \Config::get('queue.db', 0)));

		\Resque::setBackend($redis, $db);

		if ( ! empty($prefix) && is_string($prefix))
		{
			\Cli::write("*** Prefix set to $prefix\n", "blue");
			\Resque_Redis::prefix($prefix);
		}
	}

	public static function status($workers = null)
	{
		if (is_null($workers)) {
			$workers = \Resque_Worker::all();
		}
		else
		{
			$workers = array(\Resque_Worker::find($workers));
		}

		if (empty($workers))
		{
			\Cli::write("*** No workers running", 'red');
		}

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
			\Cli::write("\t* Failed: " . $worker->getStat('failed'), 'red');
		}

		\Cli::write("\n$count of " . count($workers) . " Workers Working\n", "yellow");

		\Cli::write("Token\t\t\t\t\tWorker\t\t\tQueue\t\tClass\t\tArgs", "blue");

		foreach ($jobs as $job)
		{
			\Cli::write($job['payload']['id'] . "\t" . $job["worker"] . "\t" . $job["queue"] . "\t\t" . $job['payload']['class'] . "\t" . json_encode($job['payload']['args']));
		}
	}

	public static function stop($workers = null)
	{
		if (is_null($workers)) {
			$workers = \Resque_Worker::all();
		}
		else
		{
			$workers = array(\Resque_Worker::find($workers));
		}

		foreach ($workers as $worker) {
			if ($worker instanceof \Resque_Worker)
			{
				$pid = explode(':', $worker);
				\Cli::write("*** Stopping worker " . $worker, 'green');
				posix_kill($pid[1], SIGQUIT);
				$worker->pruneDeadWorkers();
			}
		}
	}

	public static function pause($workers = null)
	{
		if (is_null($workers)) {
			$workers = \Resque_Worker::all();
		}
		else
		{
			$workers = array(\Resque_Worker::find($workers));
		}

		foreach ($workers as $worker) {
			if ($worker instanceof \Resque_Worker)
			{
				$pid = explode(':', $worker);
				\Cli::write("*** Pause worker " . $worker, 'green');
				posix_kill($pid[1], SIGUSR2);
			}
		}
	}

	public static function unpause($workers = null)
	{
		if (is_null($workers)) {
			$workers = \Resque_Worker::all();
		}
		else
		{
			$workers = array(\Resque_Worker::find($workers));
		}

		foreach ($workers as $worker) {
			if ($worker instanceof \Resque_Worker)
			{
				$pid = explode(':', $worker);
				\Cli::write("*** Unpause worker " . $worker, 'green');
				posix_kill($pid[1], SIGCONT);
			}
		}
	}
}
/* End of file tasks/resque.php */
