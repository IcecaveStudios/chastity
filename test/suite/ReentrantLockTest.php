<?php
namespace Icecave\Chastity;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Chastity\Exception\LockAcquisitionException;
use Icecave\Chastity\Exception\LockNotAcquiredException;
use PHPUnit_Framework_TestCase;

class ReentrantLockTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->innerLock = Phony::mock(LockInterface::class);

        $this
            ->innerLock
            ->resource
            ->returns('<resource>');

        $this
            ->innerLock
            ->tryAcquire
            ->returns(true);

        $this
            ->innerLock
            ->isAcquired
            ->returns(true);

        $this->ttl = 10;
        $this->timeout = 30;

        $this->lock = new ReentrantLock(
            $this->innerLock->mock()
        );
    }

    public function testDestruct()
    {
        $this->lock->tryAcquire($this->ttl);
        $this->lock->__destruct();

        $this
            ->innerLock
            ->release
            ->once()
            ->called();
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
            ->innerLock
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
            ->innerLock
            ->isAcquired
            ->once()
            ->called();
    }

    public function testIsAcquiredAfterFailedAcquisition()
    {
        $this
            ->innerLock
            ->tryAcquire
            ->returns(false);

        $this->lock->tryAcquire($this->ttl);

        $this->assertFalse(
            $this->lock->isAcquired()
        );

        // The internal state should indicate that no lock is acquired and hence
        // the driver will not be asked ...
        $this
            ->innerLock
            ->isAcquired
            ->never()
            ->called();
    }

    public function testIsAcquiredWhenLockHasExpired()
    {
        $this
            ->innerLock
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
            ->innerLock
            ->isAcquired
            ->once()
            ->called();
    }

    public function testAcquire()
    {
        $this->lock->acquire($this->ttl);

        $this
            ->innerLock
            ->acquire
            ->once()
            ->calledWith($this->ttl, INF);

        $this->assertTrue(
            $this->lock->isAcquired()
        );
    }

    public function testAcquireWithTimeout()
    {
        $this->lock->acquire($this->ttl, $this->timeout);

        $this
            ->innerLock
            ->acquire
            ->once()
            ->calledWith($this->ttl, $this->timeout);

        $this->assertTrue(
            $this->lock->isAcquired()
        );
    }

    public function testAcquireFailure()
    {
        $this
            ->innerLock
            ->acquire
            ->throws(
                new LockAcquisitionException('<resource>')
            );

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

    public function testTryAcquire()
    {
        $isAcquired = $this->lock->tryAcquire($this->ttl);

        $this
            ->innerLock
            ->tryAcquire
            ->once()
            ->calledWith($this->ttl, INF);

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
            ->innerLock
            ->tryAcquire
            ->once()
            ->calledWith($this->ttl, $this->timeout);

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
            ->innerLock
            ->tryAcquire
            ->returns(false);

        $this->assertFalse(
            $this->lock->tryAcquire($this->ttl)
        );
    }

    public function testExtend()
    {
        $this->lock->extend($this->ttl);

        $this
            ->innerLock
            ->extend
            ->calledWith($this->ttl);
    }

    public function testRelease()
    {
        $this->lock->tryAcquire($this->ttl);
        $this->lock->release();

        $this
            ->innerLock
            ->release
            ->once()
            ->called();

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

        $this->lock->release();
    }

    public function testNestedAcquisition()
    {
        // First acquisition ...
        $this->assertTrue(
            $this->lock->tryAcquire($this->ttl)
        );
        $this->assertTrue(
            $this->lock->isAcquired()
        );

        $this
            ->innerLock
            ->tryAcquire
            ->once()
            ->calledWith($this->ttl, INF);

        // Second acquisition ...
        $this->assertTrue(
            $this->lock->tryAcquire($this->ttl)
        );
        $this->assertTrue(
            $this->lock->isAcquired()
        );

        // Third acquisition ...
        // Note use of acquire() instead of tryAcquire(), they must be able to be mixed in this manner.
        $this->lock->acquire($this->ttl);

        $this->assertTrue(
            $this->lock->isAcquired()
        );

        // Even after third acquisition the lock is only actually acquried
        // once ...
        $this
            ->innerLock
            ->tryAcquire
            ->once()
            ->called();

        $this
            ->innerLock
            ->acquire
            ->never()
            ->called();

        // First release ...
        $this->lock->release();

        $this
            ->innerLock
            ->release
            ->never()
            ->called();

        $this->assertTrue(
            $this->lock->isAcquired()
        );

        // Second release ...
        $this->lock->release();

        $this
            ->innerLock
            ->release
            ->never()
            ->called();

        $this->assertTrue(
            $this->lock->isAcquired()
        );

        // Third release ...
        $this->lock->release();

        $this
            ->innerLock
            ->release
            ->once()
            ->called();

        $this->assertFalse(
            $this->lock->isAcquired()
        );
    }
}
