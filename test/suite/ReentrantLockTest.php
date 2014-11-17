<?php
namespace Icecave\Chastity;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Chastity\Exception\LockException;
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

        $this->ttl     = 10;
        $this->timeout = 30;

        $this->lock = new ReentrantLock(
            $this->innerLock->mock()
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
            ->innerLock
            ->acquire
            ->once()
            ->calledWith($this->ttl, INF);
    }

    public function testAcquireWithTimeout()
    {
        $this->lock->acquire($this->ttl, $this->timeout);

        $this
            ->innerLock
            ->acquire
            ->once()
            ->calledWith($this->ttl, $this->timeout);
    }

    public function testAcquireFailure()
    {
        $this
            ->innerLock
            ->acquire
            ->throws(
                new LockException('<resource>')
            );

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
            ->innerLock
            ->tryAcquire
            ->once()
            ->calledWith($this->ttl, INF);

        $this->assertTrue(
            $isAcquired
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
    }

    public function testReleaseWhenNotAcquired()
    {
        $this->lock->release();

        $this
            ->innerLock
            ->release
            ->never()
            ->called();
    }

    public function testNestedAcquisition()
    {
        // First acquisition ...
        $this->assertTrue(
            $this->lock->tryAcquire($this->ttl)
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

        // Third acquisition ...
        // Note use of acquire() instead of tryAcquire(), they must be able to be mixed in this manner.
        $this->lock->acquire($this->ttl);

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

        // Second release ...
        $this->lock->release();

        $this
            ->innerLock
            ->release
            ->never()
            ->called();

        // Third release ...
        $this->lock->release();

        $this
            ->innerLock
            ->release
            ->once()
            ->called();
    }
}
