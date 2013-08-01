<?php

namespace Fuel\Core;

class Job {

	public function before() {}

	public function run() {}

	public function after() {}

	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'setUp':
				return $this->before();
				break;
			case 'perform':
				return $this->run();
				break;
			case 'tearDown':
				return $this->after();
				break;
			default:
				# code...
				break;
		}
	}
}
