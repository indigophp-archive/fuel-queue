<?php

namespace Fuel\Core;

abstract class Job {

	abstract public function before();

	abstract function run();

	abstract function after();

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
