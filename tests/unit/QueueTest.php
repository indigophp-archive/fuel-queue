<?php

/*
 * This file is part of the Fuel Queue package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Fuel;

use Codeception\TestCase\Test;

/**
 * Tests for Queue
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @coversDefaultClass Indigo\Fuel\Queue
 * @group              Queue
 */
class QueueTest extends Test
{
	public function _before()
	{
		Queue::_init();
		\Config::set('queue.queue.test', \Mockery::mock('Indigo\\Queue\\Connector\\ConnectorInterface'));
	}

	/**
	 * @covers ::forge
	 */
	public function testForge()
	{
		$class = Queue::forge('test');

		$this->assertInstanceOf('Indigo\\Queue\\Queue', $class);
		$this->assertInstanceOf('Indigo\\Queue\\Connector\\ConnectorInterface', $class->getConnector());
	}

	/**
	 * @covers ::resolveConnector
	 */
	public function testResolve()
	{
		$connector = Queue::resolveConnector('test');

		$this->assertInstanceOf('Indigo\\Queue\\Connector\\ConnectorInterface', $connector);
	}

	/**
	 * @covers            ::resolveConnector
	 * @expectedException InvalidArgumentException
	 */
	public function testResolveFailure()
	{
		Queue::resolveConnector('THIS_SHOULD_NEVER_EXIST');
	}
}
