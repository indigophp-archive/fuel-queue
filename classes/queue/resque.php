<?php

namespace Queue;

class Queue_Resque extends Queue_Driver
{
	public function enqueue($job, $args = null)
	{
		return \Resque::enqueue($this->queue, $job, $args);
	}
}