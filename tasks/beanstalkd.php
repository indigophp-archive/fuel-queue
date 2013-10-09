<?php

namespace Fuel\Tasks;

class Beanstalkd
{
	protected $logger;

	public function __construct()
	{
		// Initialize logger
		$logger = clone \Log::instance();
		$logger->popHandler();

		// Determine the filename
		$filepath = \Config::get('log_path') . date('Y/m') . DS;
		$filename = $filepath . date('d') . '.php';

		// Create the streamhandler, and activate the handler (copied from Log class)
		$stream = new \Monolog\Handler\StreamHandler($filename, \Monolog\Logger::NOTICE);
		$formatter = new \Monolog\Formatter\ContextLineFormatter("%level_name% - %datetime% --> %message%".PHP_EOL, "Y-m-d H:i:s");
		$stream->setFormatter($formatter);
		$logger->pushHandler($stream);
		$stream = new \Monolog\Handler\ConsoleHandler(\Monolog\Logger::NOTICE);
		$formatter = new \Monolog\Formatter\ContextLineFormatter("%message%".PHP_EOL, "Y-m-d H:i:s");
		$stream->setFormatter($formatter);
		$logger->pushHandler($stream);
		$this->logger = $logger;

		// Register shutdown function to catch exit
		\Event::register('shutdown', function() use($logger) {
			$logger->log(\Monolog\Logger::WARNING, 'Worker {pid} is stopping', array('pid' => getmypid()));
		});
	}

	public function run()
	{

	}

	public function stats($queue = null)
	{
		$this->instance = new \Pheanstalk_Pheanstalk('127.0.0.1');
		var_dump($this->instance->statsTube('default2')); exit;
	}

}
