<?php

namespace Fuel\Tasks;

class Beanstalkd
{

	public function run()
	{
		$this->instance = new \Pheanstalk_Pheanstalk('127.0.0.1');

		while (true)
		{
			$job = $this->instance
				->watch('default2')
				->ignore('default')
				->reserve();

			if ($job instanceof \Pheanstalk_Job)
			{
				$stat = $this->instance->statsJob($job);
				if ($stat->reserves - $stat->releases > 4)
				{
					$this->instance->bury($job);
					continue;
				}
				else
				{
					$a = json_decode($job->getData(), true);
					$class = $a['job'];
					$class = new $class();
					$class->args=$a['args'];

					$class->fire();
					$this->instance->delete($job);
				}
			}
		}
	}

	public function stats()
	{
		$this->instance = new \Pheanstalk_Pheanstalk('127.0.0.1');
		var_dump($this->instance->statsTube('default2')); exit;
	}

}
