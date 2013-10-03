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

class Queue_Beanstalkd extends Queue_Driver
{

	protected function _init()
	{
		$this->instance = new \Pheanstalk_Pheanstalk($this->get_config('host', '127.0.0.1'), $this->get_config('port', '11300'));
	}

	/**
	 * Push a job to the queue
	 *
	 * @param	string	$job		Job name
	 * @param	array	$args		Optional array of arguments
	 * @param	int		$priority	Job priority
	 * @param	int		$delay		Delay
	 * @param	int		$ttr		TTR
	 * @return	mixed				Job id
	 */
	protected function _push($job, array $args = array(), $priority = \Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY, $delay = \Pheanstalk_PheanstalkInterface::DEFAULT_DELAY, $ttr = \Pheanstalk_PheanstalkInterface::DEFAULT_TTR)
	{
		return $this->instance
			->useTube($this->queue)
			->put(json_encode(array('job' => $job, 'args' => $args)), $priority, $delay, $ttr);
	}
}
