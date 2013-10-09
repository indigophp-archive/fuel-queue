<?php
/**
 * Fuel Queue
 *
 * @package 	Fuel
 * @subpackage	Queue
 * @version		1.0
 * @author 		Márk Sági-Kazár <mark.sagikazar@gmail.com>
 * @license 	MIT License
 * @link 		https://github.com/indigo-soft
 */

namespace Queue;

class Worker_Resque extends Worker_Driver
{
	protected function _init()
	{
		$redis = $this->get_config('host', '127.0.0.1') . ':' . $this->get_config('port', '6379');
		\Resque::setBackend($redis, $this->get_config('redis.db', 0));
		\Resque_Redis::prefix($this->get_config('redis.prefix', 'fuel'));

		$this->instance = new \Resque_Worker($this->get_config('queue', array('default')));

		// Listen for failures and call the Job class failure method
		\Resque_Event::listen('onFailure', function($e, $job) {
			if ($job instanceof \Resque_Job)
			{
				$instance = $job->getInstance();
				$instance->failure($e);
			}
		});

		// Before PHP 5.4: $this cannot be used for anonymus functions
		$event = $this->event;

		// Listen for job events
		\Resque_Event::listen('beforePerform', function($job) use($event) {
			if ($job instanceof \Resque_Job)
			{
				$j = array(
					'job'  => $job->payload['class'],
					'args' => $job->getArguments()
				);
				$event->trigger('job_start', $j);
			}
		});

		\Resque_Event::listen('afterPerform', function($job) use($event) {
			if ($job instanceof \Resque_Job)
			{
				$j = array(
					'job'  => $job->payload['class'],
					'args' => $job->getArguments()
				);
				$event->trigger('job_finish', $j);
			}
		});
	}

	public function work()
	{
		$this->instance->work(1, false);
	}
}
