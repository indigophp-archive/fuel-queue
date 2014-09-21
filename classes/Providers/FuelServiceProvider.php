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
use Indigo\Queue\Connector;
use Indigo\Queue\Queue;

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
		'queue.beanstalk',
		'queue.direct',
		'queue.iron',
		'queue.rabbit',
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

		$config = \Config::get('queue', []);
		$this->defaultConfig = \Arr::filter_keys($config, ['queues'], true);

		// We don't have defined queues
		if ($queues = \Arr::get($config, 'queues', false) and ! empty($queues))
		{
			\Config::set('queue.queues.__default__', []);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function provide()
	{
		$this->register('queue', function($dic, $name, $config = [])
		{
			$connector = $this->getConnector($dic, $name, $config);

			return $dic->resolve('Indigo\\Queue\\Queue', [$name, $connector]);
		});

		$this->register('worker', function($dic, $name, $config = [])
		{
			$connector = $this->getConnector($dic, $name, $config);

			return $dic->resolve('Indigo\\Queue\\Worker', [$name, $connector]);
		});

		$this->register('queue.beanstalk', function($dic, $name, array $config = [])
		{
			$pheanstalk = $dic->resolve('Pheanstalk\\Pheanstalk', \Arr::filter_keys($config, ['host', 'port']));

			return $dic->resolve('Indigo\\Queue\\Connector\\BeanstalkConnector', [$pheanstalk]);
		});

		$this->register('queue.direct', function($dic)
		{
			return $dic->resolve('Indigo\\Queue\\Connector\\DirectConnector');
		});
	}

	public function getConnector($dic, $name, $config = [])
	{
		if ( ! is_array($config))
		{
			$config = ['connector' => $config];
		}

		$config = array_merge($this->defaultConfig, \Config::get('queue.queues.', $name, []), $config);

		// determine the connector to load
		if ($config['connector'] instanceof Connector)
		{
			$connector = $config['connector'];
		}
		elseif (strpos('\\', $config['connector']) !== false and class_exists($config['connector']))
		{
			$connector = $dic->resolve($config['connector'], [$config]);
		}
		else
		{
			$connector = $dic->multiton('queue.'.strtolower($config['connector']), $name, [$config]);
		}

		return $connector;
	}
}
