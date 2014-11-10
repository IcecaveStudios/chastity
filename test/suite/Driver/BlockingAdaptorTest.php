<?php
namespace Icecave\Chastity\Driver;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Interlude\Invoker;
use Icecave\Interlude\InvokerInterface;
use PHPUnit_Framework_TestCase;

class BlockingAdaptorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->driver = Phony::mock(DriverInterface::class);
        // $this->invoker = Phony::mock(InvokerInterface::class);

        $this->ttl = 10;
        $this->timeout = 30;

        $this->adaptor = new BlockingAdaptor(
            $this->driver->mock()
            // $this->invoker->mock()
        );
    }

    public function testAcquire()
    {
        $this
            ->driver
            ->acquire
            ->returns(true);

        $result = $this->adaptor->acquire(
            '<resource>',
            '<token>',
            $this->ttl,
            $this->timeout
        );

        $this
            ->driver
            ->acquire
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
            ->acquire
            ->returns(false);

        $result = $this->adaptor->acquire(
            '<resource>',
            '<token>',
            $this->ttl,
            0.1
        );

        $this
            ->driver
            ->acquire
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

    public function testIsAcquired()
    {
        $this
            ->driver
            ->isAcquired
            ->returns('<result>');

        $this->assertSame(
            '<result>',
            $this->adaptor->isAcquired('<resource>', '<token>')
        );

        $this
            ->driver
            ->isAcquired
            ->calledWith('<resource>', '<token>');
    }

    public function testExtend()
    {
        $this
            ->driver
            ->extend
            ->returns('<result>');

        $this->assertSame(
            '<result>',
            $this->adaptor->extend('<resource>', '<token>', $this->ttl)
        );

        $this
            ->driver
            ->extend
            ->calledWith('<resource>', '<token>', $this->ttl);
    }

    public function testRelease()
    {
        $this
            ->driver
            ->release
            ->returns('<result>');

        $this->assertSame(
            '<result>',
            $this->adaptor->release('<resource>', '<token>')
        );

        $this
            ->driver
            ->release
            ->calledWith('<resource>', '<token>');
    }
}
