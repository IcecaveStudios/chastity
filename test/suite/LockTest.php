<?php
namespace Icecave\Chastity;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Chastity\Driver\DriverInterface;
use Icecave\Chastity\Exception\LockException;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;

class LockTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->driver = Phony::mock(DriverInterface::class);
        $this->logger = Phony::mock(LoggerInterface::class);

        $this
            ->driver
            ->acquire
            ->returns(true);

        $this
            ->driver
            ->extend
            ->returns(true);

        $this
            ->driver
            ->release
            ->returns(true);

        $this->ttl     = 10;
        $this->timeout = 30;

        $this->lock = new Lock(
            $this->driver->mock(),
            '<resource>',
            '<token>'
        );
    }

    public function testResource()
    {
        $this->assertSame(
            '<resource>',
            $this->lock->resource()
        );
    }

    public function testAcquire()
    {
        $this->lock->acquire($this->ttl);

        $this
            ->driver
            ->acquire
            ->once()
            ->calledWith(
                '<resource>',
                '<token>',
                $this->ttl,
                INF
            );
    }

    public function testAcquireWithTimeout()
    {
        $this->lock->acquire(10, 30);

        $this
            ->driver
            ->acquire
            ->once()
            ->calledWith(
                '<resource>',
                '<token>',
                $this->ttl,
                $this->timeout
            );
    }

    public function testAcquireFailure()
    {
        $this
            ->driver
            ->acquire
            ->returns(false);

        $this->setExpectedException(
            LockException::class,
            'Unable to acquire lock: <resource>.'
        );

        $this->lock->acquire($this->ttl);
    }

    public function testTryAcquire()
    {
        $isAcquired = $this->lock->tryAcquire($this->ttl);

        $this
            ->driver
            ->acquire
            ->once()
            ->calledWith(
                '<resource>',
                '<token>',
                $this->ttl,
                INF
            );

        $this->assertTrue(
            $isAcquired
        );
    }

    public function testTryAcquireWithTimeout()
    {
        $isAcquired = $this->lock->tryAcquire($this->ttl, $this->timeout);

        $this
            ->driver
            ->acquire
            ->once()
            ->calledWith(
                '<resource>',
                '<token>',
                $this->ttl,
                $this->timeout
            );

        $this->assertTrue(
            $isAcquired
        );
    }

    public function testTryAcquireFailure()
    {
        $this
            ->driver
            ->acquire
            ->returns(false);

        $this->assertFalse(
            $this->lock->tryAcquire($this->ttl)
        );
    }

    public function testTryAcquireLogging()
    {
        $this->lock->setLogger(
            $this->logger->mock()
        );

        $this->lock->tryAcquire($this->ttl, $this->timeout);

        Phony::inOrder(
            $this->driver->acquire->called(),
            $this->logger->debug->calledWith(
                'Resource "{resource}" locked by {token} with {ttl} second TTL',
                [
                    'resource' => '<resource>',
                    'token'    => '<token>',
                    'ttl'      => $this->ttl,
                ]
            )
        );
    }

    public function testTryAcquireFailureLogging()
    {
        $this
            ->driver
            ->acquire
            ->returns(false);

        $this->lock->setLogger(
            $this->logger->mock()
        );

        $this->lock->tryAcquire($this->ttl, $this->timeout);

        Phony::inOrder(
            $this->driver->acquire->called(),
            $this->logger->debug->calledWith(
                'Resource "{resource}" could not be locked by {token} after {timeout} second timeout',
                [
                    'resource' => '<resource>',
                    'token'    => '<token>',
                    'timeout'  => $this->timeout,
                ]
            )
        );
    }

    public function testTryAcquireFailureLoggingWithZeroTimeout()
    {
        $this
            ->driver
            ->acquire
            ->returns(false);

        $this->lock->setLogger(
            $this->logger->mock()
        );

        $this->lock->tryAcquire($this->ttl, 0);

        $this->logger->debug->never()->calledWith(
            'Resource "{resource}" could not be locked by {token} after {timeout} second timeout',
            [
                'resource' => '<resource>',
                'token'    => '<token>',
                'timeout'  => $this->timeout,
            ]
        );
    }

    public function testExtend()
    {
        $this->lock->extend($this->ttl);

        $this
            ->driver
            ->extend
            ->calledWith(
                '<resource>',
                '<token>',
                $this->ttl
            );
    }

    public function testExtendWhenNotAcquired()
    {
        $this
            ->driver
            ->extend
            ->returns(false);

        $this->lock->tryAcquire($this->ttl);

        $this->setExpectedException(
            LockException::class,
            'Unable to acquire lock: <resource>.'
        );

        $this->lock->extend($this->ttl);
    }

    public function testExtendLogging()
    {
        $this->lock->setLogger(
            $this->logger->mock()
        );

        $this->lock->extend($this->ttl);

        Phony::inOrder(
            $this->driver->extend->called(),
            $this->logger->debug->calledWith(
                'Resource "{resource}" lock extended by {token} with {ttl} second TTL',
                [
                    'resource' => '<resource>',
                    'token'    => '<token>',
                    'ttl'      => $this->ttl,
                ]
            )
        );
    }

    public function testRelease()
    {
        $this->lock->release();

        $this
            ->driver
            ->release
            ->once()
            ->calledWith('<resource>', '<token>');
    }

    public function testReleaseWhenNotAcquired()
    {
        $this
            ->driver
            ->release
            ->returns(false);

        $this->lock->release();

        $this
            ->driver
            ->release
            ->once()
            ->calledWith('<resource>', '<token>');
    }

    public function testReleaseLogging()
    {
        $this
            ->driver
            ->release
            ->returns(true);

        $this->lock->setLogger(
            $this->logger->mock()
        );

        $this->lock->release();

        Phony::inOrder(
            $this->driver->release->called(),
            $this->logger->debug->calledWith(
                'Resource "{resource}" released by {token}',
                [
                    'resource' => '<resource>',
                    'token'    => '<token>',
                ]
            )
        );
    }

    public function testReleaseLoggingWhenNotAcquired()
    {
        $this
            ->driver
            ->release
            ->returns(false);

        $this->lock->setLogger(
            $this->logger->mock()
        );

        $this->lock->release();

        $this
            ->logger
            ->debug
            ->never()
            ->called();
    }
}
