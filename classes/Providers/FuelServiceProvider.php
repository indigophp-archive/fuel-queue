<?php

/*
 * This file is part of the Fuel DBAL package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Fuel\Queue\Providers;

use Fuel\Dependency\ServiceProvider;

/**
 * Provides Queue service
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FuelServiceProvider extends ServiceProvider
{
	/**
	 * {@inheritdoc}
	 */
	public $provides = [
		'queue',
		'worker',
		'queue.connector.beanstalk',
		'queue.connector.direct',
		'queue.connector.iron',
		'queue.connector.rabbit',
	];

	/**
	 * Default configuration values
	 *
	 * @var []
	 */
	protected $defaultConfig = [];

	public function __construct()
	{
		\Config::load('queue', true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function provide()
	{
		$this->register('queue', function($dic, $name, $connector)
		{

		});
	}
}
