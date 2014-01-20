<?php

namespace Fuel\Tasks;

use Indigo\Queue\Worker;

class Queue
{
	/**
	 * Logger instance
	 *
	 * @var Psr\Log\LoggerInterface
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

		// Process context in message
		$processor = new \Monolog\Processor\PsrLogMessageProcessor();
		$logger->pushProcessor($processor);

		// Only log to console when it is enabled
		if (\Cli::option('c', false) === true)
		{
			$handler = new \Monolog\Handler\ConsoleHandler(\Monolog\Logger::DEBUG);
			$formatter = new \Monolog\Formatter\LineFormatter("%level_name% --> %message%".PHP_EOL, "Y-m-d H:i:s");
			$handler->setFormatter($formatter);
			$logger->pushHandler($handler);
		}

		// Add other handlers to logger through Event trigger
		\Event::instance('queue')->trigger('logger', $logger);

		$this->logger = $logger;

		// Listener should not simply stop
		$this->shutdown = function() use($logger) {
			$pid = getmypid();
			$logger->info('Worker ' . $pid . ' is stopping', compact('pid'));
		};
	}

	/**
	 * Resolve worker
	 *
	 * @param	mixed	$queue
	 * @return	Worker
	 */
	protected function _resolve($queue)
	{
		$connector = \Queue::instance($queue)->getConnector();

		return new Worker($queue, $connector);
	}

	/**
	 * Listen to queue
	 *
	 * @param	mixed	$queue
	 */
	public function run($queue = 'default')
	{
		$worker = $this->_resolve($queue);

		// Register shutdown function to catch exit
		\Event::register('shutdown', $this->shutdown);

		$interval = \Cli::option('interval', \Cli::option('i', 5));

		$worker->listen($interval);
	}

	/**
	 * Process a job from a queue
	 *
	 * @param	mixed	$queue
	 */
	public function work($queue = 'default')
	{
		$worker = $this->_resolve($queue);

		$worker->work();
	}
}
