<?php
namespace Icecave\Chastity\Driver;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Interlude\InvokerInterface;
use PHPUnit_Framework_TestCase;

class PollingDriverTraitTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->ttl     = 10;
        $this->timeout = 30;

        $this->driver = Phony::mock(PollingDriverTrait::class);
    }

    public function testAcquire()
    {
        $this
            ->driver
            ->poll
            ->returns(true);

        $result = $this
            ->driver
            ->mock()
            ->acquire(
                '<resource>',
                '<token>',
                $this->ttl,
                $this->timeout
            );

        $this
            ->driver
            ->poll
            ->once()
            ->calledWith(
                '<resource>',
                '<token>',
                $this->ttl
            );

        $this->assertTrue(
            $result
        );
    }

    public function testAcquireTimeout()
    {
        $this
            ->driver
            ->poll
            ->returns(false);

        $result = $this
            ->driver
            ->mock()
            ->acquire(
                '<resource>',
                '<token>',
                $this->ttl,
                0.1
            );

        $this
            ->driver
            ->poll
            ->atLeast(1)
            ->calledWith(
                '<resource>',
                '<token>',
                $this->ttl
            );

        $this->assertFalse(
            $result
        );
    }

    public function testPollPeriod()
    {
        $driver = $this->driver->mock();

        $this->assertSame(
            0.1,
            $driver->pollPeriod()
        );

        $driver->setPollPeriod(0.25);

        $this->assertSame(
            0.25,
            $driver->pollPeriod()
        );
    }

    public function testInvoker()
    {
        $driver = $this->driver->mock();

        $invoker = $driver->invoker();

        $this->assertInstanceOf(
            InvokerInterface::class,
            $invoker
        );

        $invoker = Phony::mock(InvokerInterface::class)->mock();

        $driver->setInvoker($invoker);

        $this->assertSame(
            $invoker,
            $driver->invoker()
        );
    }
}
