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

class Worker_Beanstalkd extends Worker_Driver
{
	protected function _init()
	{
		$this->instance = new \Pheanstalk_Pheanstalk($this->get_config('host', '127.0.0.1'), $this->get_config('port', '11300'));

		$queues = $this->get_config('queue', array('default'));

		in_array('default', $queues) || $this->instance->ignore('default');

		foreach ($queues as $queue)
		{
			$this->instance->watch($queue);
		}
	}

	public function work()
	{
		while (true)
		{
			$job = $this->instance->reserve();

			if ($job instanceof \Pheanstalk_Job)
			{
				$stat = $this->instance->statsJob($job);
				if ($stat->reserves - $stat->releases >= $this->get_config('max_retry', 5))
				{
					$this->instance->bury($job);
					continue;
				}
				else
				{
					$j = json_decode($job->getData(), true);
					$class = $j['job'];
					$class = new $class();
					$class->args=$j['args'];

					try
					{
						$class->before();
						$class->run();
						$class->after();
						$this->instance->delete($job);
					}
					catch (\WorkerException $e)
					{

					}
				}
			}
		}
	}
}
