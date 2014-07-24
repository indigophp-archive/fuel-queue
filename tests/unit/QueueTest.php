<?php

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
	 * @covers            ::forge
	 * @expectedException InvalidArgumentException
	 */
	public function testForgeFailure()
	{
		Queue::forge('THIS_SHOULD_NEVER_EXIST');
	}
}
