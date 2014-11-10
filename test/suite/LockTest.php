<?php
namespace Icecave\Chastity;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Chastity\Driver\BlockingDriverInterface;
use Icecave\Chastity\Exception\LockAcquisitionException;
use Icecave\Chastity\Exception\LockAlreadyAcquiredException;
use Icecave\Chastity\Exception\LockNotAcquiredException;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;

class LockTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->driver = Phony::mock(BlockingDriverInterface::class);
        $this->logger = Phony::mock(LoggerInterface::class);

        $this
            ->driver
            ->acquire
            ->returns(true);

        $this
            ->driver
            ->isAcquired
            ->returns(true);

        $this
            ->driver
            ->extend
            ->returns(true);

        $this
            ->driver
            ->release
            ->returns(true);

        $this->ttl = 10;
        $this->timeout = 30;

        $this->lock = new Lock(
            $this->driver->mock(),
            '<resource>',
            '<token>'
        );
    }

    public function testDestruct()
    {
        $this->lock->__destruct();

        $this
            ->driver
            ->release
            ->never()
            ->called();
    }

    public function testDestructWhenAcquired()
    {
        $this->lock->tryAcquire($this->ttl);
        $this->lock->__destruct();

        $this
            ->driver
            ->release
            ->once()
            ->calledWith('<resource>', '<token>');
    }

    public function testResource()
    {
        $this->assertSame(
            '<resource>',
            $this->lock->resource()
        );
    }

    public function testIsAcquired()
    {
        $this->assertFalse(
            $this->lock->isAcquired()
        );

        // The internal state should indicate that no lock is acquired and hence
        // the driver will not be asked ...
        $this
            ->driver
            ->isAcquired
            ->never()
            ->called();

        $this->lock->tryAcquire($this->ttl);

        $this->assertTrue(
            $this->lock->isAcquired()
        );

        // The internal state should indicate that the lock has been acquired,
        // so the driver is asked to ensure that lock is still held ...
        $this
            ->driver
            ->isAcquired
            ->once()
            ->called();
    }

    public function testIsAcquiredAfterFailedAcquisition()
    {
        $this
            ->driver
            ->acquire
            ->returns(false);

        $this->lock->tryAcquire($this->ttl);

        $this->assertFalse(
            $this->lock->isAcquired()
        );

        // The internal state should indicate that no lock is acquired and hence
        // the driver will not be asked ...
        $this
            ->driver
            ->isAcquired
            ->never()
            ->called();
    }

    public function testIsAcquiredWhenLockHasExpired()
    {
        $this
            ->driver
            ->isAcquired
            ->returns(false);

        $this->lock->tryAcquire($this->ttl);

        $this->assertFalse(
            $this->lock->isAcquired()
        );

        $this->assertFalse(
            $this->lock->isAcquired()
        );

        $this
            ->driver
            ->isAcquired
            ->once()
            ->called();
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

        $this->assertTrue(
            $this->lock->isAcquired()
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

        $this->assertTrue(
            $this->lock->isAcquired()
        );
    }

    public function testAcquireFailure()
    {
        $this
            ->driver
            ->acquire
            ->returns(false);

        $this->setExpectedException(
            LockAcquisitionException::class,
            'Unable to acquire lock: <resource>.'
        );

        try {
            $this->lock->acquire($this->ttl);
        } catch (LockAcquisitionException $e) {
            $this->assertFalse(
                $this->lock->isAcquired()
            );

            throw $e;
        }
    }

    public function testAcquireAlreadyAcquiredFailure()
    {
        $this->setExpectedException(
            LockAlreadyAcquiredException::class,
            'Lock has already been acquired: <resource>.'
        );

        $this->lock->acquire($this->ttl);
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

        $this->assertTrue(
            $this->lock->isAcquired()
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

        $this->assertTrue(
            $this->lock->isAcquired()
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

    public function testTryAcquireAlreadyAcquiredFailure()
    {
        $this->setExpectedException(
            LockAlreadyAcquiredException::class,
            'Lock has already been acquired: <resource>.'
        );

        $this->lock->tryAcquire($this->ttl);
        $this->lock->tryAcquire($this->ttl);
    }

    public function testTryAcquireLogging()
    {
        $this->lock->setLogger(
            $this->logger->mock()
        );

        $this->lock->tryAcquire($this->ttl, $this->timeout);

        Phony::inOrder(
            $this->logger->debug->calledWith(
                'Resource "{resource}" lock request from {token} with {ttl} second TTL and {timeout} second timeout',
                [
                    'resource' => '<resource>',
                    'token'    => '<token>',
                    'ttl'      => $this->ttl,
                    'timeout'  => $this->timeout,
                ]
            ),
            $this->driver->acquire->called(),
            $this->logger->debug->calledWith(
                'Resource "{resource}" successfully locked by {token}',
                [
                    'resource' => '<resource>',
                    'token'    => '<token>',
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
            $this->logger->debug->calledWith(
                'Resource "{resource}" lock request from {token} with {ttl} second TTL and {timeout} second timeout',
                [
                    'resource' => '<resource>',
                    'token'    => '<token>',
                    'ttl'      => $this->ttl,
                    'timeout'  => $this->timeout,
                ]
            ),
            $this->driver->acquire->called(),
            $this->logger->debug->calledWith(
                'Resource "{resource}" could not be locked by {token}',
                [
                    'resource' => '<resource>',
                    'token'    => '<token>',
                ]
            )
        );
    }

    public function testExtend()
    {
        $this->lock->tryAcquire($this->ttl);
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
        $this->setExpectedException(
            LockNotAcquiredException::class,
            'Lock has not been acquired: <resource>.'
        );

        try {
            $this->lock->extend($this->ttl);
        } catch (Exception $e) {
            $this
                ->driver
                ->extend
                ->never()
                ->called();

            throw $e;
        }
    }

    public function testExtendWhenLockHasExpired()
    {
        $this
            ->driver
            ->extend
            ->returns(false);

        $this->lock->tryAcquire($this->ttl);

        $this->setExpectedException(
            LockNotAcquiredException::class,
            'Lock has not been acquired: <resource>.'
        );

        $this->lock->extend($this->ttl);
    }

    public function testExtendLogging()
    {
        $this->lock->tryAcquire($this->ttl);

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
        $this->lock->tryAcquire($this->ttl);
        $this->lock->release();

        $this
            ->driver
            ->release
            ->once()
            ->calledWith('<resource>', '<token>');

        $this->assertFalse(
            $this->lock->isAcquired()
        );
    }

    public function testReleaseWhenNotAcquired()
    {
        $this->setExpectedException(
            LockNotAcquiredException::class,
            'Lock has not been acquired: <resource>.'
        );

        try {
            $this->lock->release();
        } catch (Exception $e) {
            $this
                ->driver
                ->release
                ->never()
                ->called();

            throw $e;
        }
    }

    public function testReleaseWhenLockHasExpired()
    {
        $this
            ->driver
            ->release
            ->returns(false);

        $this->lock->tryAcquire($this->ttl);

        $this->setExpectedException(
            LockNotAcquiredException::class,
            'Lock has not been acquired: <resource>.'
        );

        $this->lock->release();
    }

    public function testReleaseLogging()
    {
        $this->lock->tryAcquire($this->ttl);

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
}
