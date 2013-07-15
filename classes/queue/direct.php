<?php

namespace Queue;

class Queue_Direct extends Queue_Driver
{

	public function push($job, array $args = array())
	{

		if( ! class_exists($job, true))
		{
			throw new \FuelException('Could not find Job: ' . $job);
		}

		$job = new $job;
		$job->args = $args;

		$job->setUp();
		$job->perform();
		$job->tearDown();
	}
}
