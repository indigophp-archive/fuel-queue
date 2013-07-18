<?php

namespace Queue;

class Queue_Direct extends Queue_Driver
{

	protected function _init() {}

	protected function _push($job, array $args = array())
	{

		$job = new $job;
		$job->args = $args;

		$job->before();
		$job->run();
		$job->after();
	}
}
