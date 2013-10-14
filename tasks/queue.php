<?php

namespace Fuel\Tasks;

use \Phresque\Worker;

class Queue
{
	protected $logger;

	public function __construct()
	{
		// Initialize logger
		$logger = clone \Log::instance();

		// Get original handler
		$handler = $logger->popHandler();
		$formatter = new \Monolog\Formatter\ContextLineFormatter("%level_name% - %datetime% --> %message% - %context%".PHP_EOL, "Y-m-d H:i:s");
		$handler->setFormatter($formatter);
		$logger->pushHandler($handler);


		// Only log to console when it is enabled
		$console = \Cli::option('c', false);

		if ($console)
		{
			// Console handler
			$handler = new \Monolog\Handler\ConsoleHandler(\Monolog\Logger::NOTICE);
			$formatter = new \Monolog\Formatter\ContextLineFormatter("%level_name% --> %message% - %context%".PHP_EOL, "Y-m-d H:i:s");
			$handler->setFormatter($formatter);
			$logger->pushHandler($handler);
		}

		// Add other handlers to logger through Event trigger
		\Event::instance('queue')->trigger('logger', $logger);

		$this->logger = $logger;

		// Register shutdown function to catch exit
		\Event::register('shutdown', function() use($logger) {
			$logger->warning('Worker {pid} is stopping', array('pid' => getmypid()));
		});
	}

	public function run($connection = 'default')
	{
		$queue = \Cli::option('queue', \Cli::option('q'));
		$driver = \Cli::option('driver', \Cli::option('d'));

		$config = \Arr::merge(\Config::get('queue.defaults'), \Config::get('queue.connections.' . $connection, array()));

		is_null($queue) or $config['queue'] = $queue;
		is_null($driver) or $config['driver'] = $driver;

		$worker = new Worker($config['queue'], $config['driver'], $config['connection']);
		$worker->setLogger($this->logger);
		$worker->listen();
	}
}
