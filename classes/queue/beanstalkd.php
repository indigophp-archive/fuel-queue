<?php

namespace Queue;

class Queue_Beanstalkd extends Queue_Driver
{

	protected function _init()
	{
		$this->instance = new \Pheanstalk_Pheanstalk('127.0.0.1');
	}

	public function push($job, array $args = array())
	{

		if( ! class_exists($job, true))
		{
			throw new \FuelException('Could not find Job: ' . $job);
		}

		$this->instance
  			->useTube('default2')
  			->put(json_encode(array('job' => $job, 'args' => array('arg1'))), 1024, 10);
	}
}
