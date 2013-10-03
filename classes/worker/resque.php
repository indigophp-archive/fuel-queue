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
		$this->instance->logLevel = $this->get_config('log', \Resque_Worker::LOG_NORMAL);
	}

	public function work()
	{
		$this->instance->work(1, false);
	}
}
