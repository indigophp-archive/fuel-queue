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
use Fuel\Dependency\ResolveException;
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
			$config = $this->resolveConfig($name, $config);

			$connector = $this->resolveConnector($dic, $name, $config);

			$logger = $this->resolveLogger($dic, $name, $config);

			$queue = $dic->resolve('Indigo\\Queue\\Queue', [$name, $connector]);

			return $queue->setLogger($logger);
		});

		$this->register('worker', function($dic, $name, $config = [])
		{
			$config = $this->resolveConfig($name, $config);

			$connector = $this->resolveConnector($dic, $name, $config);

			$logger = $this->resolveLogger($dic, $name, $config);

			$worker = $dic->resolve('Indigo\\Queue\\Worker', [$name, $connector]);

			return $worker->setLogger($logger);
		});

		$this->register('queue.beanstalk', function($dic, array $config = [])
		{
			$config = \Arr::filter_keys($config, ['host', 'port']);
			$pheanstalk = $dic->resolve('Pheanstalk\\Pheanstalk', $config);

			return $dic->resolve('Indigo\\Queue\\Connector\\BeanstalkConnector', [$pheanstalk]);
		});

		$this->register('queue.direct', function($dic)
		{
			return $dic->resolve('Indigo\\Queue\\Connector\\DirectConnector');
		});
	}

	/**
	 * Resolves config for an instance
	 *
	 * @param string $name
	 * @param mixed  $config
	 *
	 * @return []
	 */
	protected function resolveConfig($name, $config)
	{
		if ( ! is_array($config))
		{
			$config = ['connector' => $config];
		}

		return array_merge($this->defaultConfig, \Config::get('queue.queues.'.$name, []), $config);
	}

	/**
	 * Resolves a connector
	 *
	 * @param DiC    $dic
	 * @param string $name
	 * @param []     $config
	 *
	 * @return Indigo\Queue\Connector
	 */
	public function resolveConnector($dic, $name, array $config)
	{
		// determine the connector to load
		if ($config['connector'] instanceof Connector)
		{
			$connector = $config['connector'];
		}
		elseif (strpos('\\', $config['connector']) !== false and class_exists($config['connector']))
		{
			$connector = $dic->multiton($config['connector'], $name, [$config]);
		}
		else
		{
			$connector = $dic->multiton('queue.'.strtolower($config['connector']), $name, [$config]);
		}

		return $connector;
	}

	/**
	 * Resolves a logger
	 *
	 * @param DiC    $dic
	 * @param string $name
	 * @param []     $config
	 *
	 * @return Psr\Log\LoggerInterface
	 */
	protected function resolveLogger($dic, $name, array $config)
	{
		try
		{
			$logger = $dic->resolve('logger.'.\Arr::get($config, 'logger'));
		}
		catch (ResolveException $e)
		{
			$logger = $dic->resolve('Psr\\Log\\NullLogger');
		}

		return $logger;
	}
}
