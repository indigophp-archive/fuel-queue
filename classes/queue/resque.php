<?php

namespace Queue;

class Queue_Resque extends Queue_Driver
{
	protected function _init()
	{
		\Resque::setBackend($this->get_config('redis', '127.0.0.1:6379'), $this->get_config('db', 0));
		\Resque_Redis::prefix($this->get_config('prefix', 'fuel'));
	}

	public function enqueue($job, array $args = array())
	{

		if( ! class_exists($job, true))
		{
			throw new \FuelException('Could not find Job: ' . $job);
		}

		return \Resque::enqueue($this->queue, $job, $args);
	}
}