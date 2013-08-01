<?php

namespace Queue;

class Worker_Resque extends Worker_Driver
{
	protected function _init()
	{
		$this->instance = new \Resque_Worker($this->queue);
		$this->instance->logLevel = $this->get_config('log', \Resque_Worker::LOG_NORMAL)
	}

	public function work()
	{
		$worker->work((int) $this->get_config('interval', 3), (bool) $this->get_config('blocking', false));
	}
}
