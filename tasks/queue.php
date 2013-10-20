<?php

namespace Fuel\Tasks;

use \Phresque\Worker;

class Queue
{
	/**
	 * Logger instance
	 *
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger;

	/**
	 * Shutdown callable
	 *
	 * @var callable
	 */
	protected $shutdown;

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
			$handler = new \Monolog\Handler\ConsoleHandler(\Monolog\Logger::DEBUG);
			$formatter = new \Monolog\Formatter\ContextLineFormatter("%level_name% --> %message% - %context%".PHP_EOL, "Y-m-d H:i:s");
			$handler->setFormatter($formatter);
			$logger->pushHandler($handler);
		}

		// Add other handlers to logger through Event trigger
		\Event::instance('queue')->trigger('logger', $logger);

		$this->logger = $logger;

		$this->shutdown = function() use($logger) {
			$logger->warning('Worker {pid} is stopping', array('pid' => getmypid()));
		};
	}

	public function run($queue = 'default')
	{
		$config = array();

		$driver = \Cli::option('driver', \Cli::option('d'));
		is_null($driver) or $config['driver'] = $driver;

		$queue = \Queue::instance($queue, $config);

		$worker = new Worker($queue);
		$worker->setLogger($this->logger);

		// Register shutdown function to catch exit
		\Event::register('shutdown', $this->shutdown);

		$worker->listen();
	}

	public function work($queue = 'default')
	{
		$config = array();

		$driver = \Cli::option('driver', \Cli::option('d'));
		is_null($driver) or $config['driver'] = $driver;

		$queue = \Queue::instance($queue, $config);

		$worker = new Worker($queue);
		$worker->setLogger($this->logger);

		$worker->work();
	}
}
