<?php
namespace Icecave\Chastity;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Chastity\Exception\LockDetachedException;
use Icecave\Chastity\Exception\LockNotAcquiredException;
use PHPUnit_Framework_TestCase;

class ScopedLockTest extends PHPUnit_Framework_TestCase
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

        $this->ttl     = 10;
        $this->timeout = 30;

        $this->lock = new ScopedLock(
            $this->innerLock->mock()
        );
    }

    public function testDestruct()
    {
        $this->lock->__destruct();

        $this
            ->innerLock
            ->release
            ->once()
            ->called();
    }

    public function testDestructWhenDetached()
    {
        $this->lock->detach();

        $this->lock->__destruct();

        $this
            ->innerLock
            ->release
            ->never()
            ->called();
    }

    public function testDestructSilencesReleaseFailures()
    {
        $this
            ->innerLock
            ->release
            ->throws(new LockNotAcquiredException('<resource>'));

        $this->lock->__destruct();

        $this
            ->innerLock
            ->release
            ->once()
            ->called();
    }

    public function testDetach()
    {
        $this->assertSame(
            $this->innerLock->mock(),
            $this->lock->detach()
        );
    }

    public function testDetachWhenDetached()
    {
        $this->lock->detach();

        $this->setExpectedException(
            LockDetachedException::class
        );

        $this->lock->detach();
    }

    public function testResource()
    {
        $this->assertSame(
            '<resource>',
            $this->lock->resource()
        );
    }

    public function testResourceWhenDetached()
    {
        $this->lock->detach();

        $this->setExpectedException(
            LockDetachedException::class
        );

        $this->lock->resource();
    }

    public function testIsAcquired()
    {
        $this->assertTrue(
            $this->lock->isAcquired()
        );

        // The internal state should indicate that no lock is acquired and hence
        // the driver will not be asked ...
        $this
            ->innerLock
            ->isAcquired
            ->called();
    }

    public function testIsAcquiredWhenDetached()
    {
        $this->lock->detach();

        $this->setExpectedException(
            LockDetachedException::class
        );

        $this->lock->isAcquired();
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

    public function testAcquireWhenDetached()
    {
        $this->lock->detach();

        $this->setExpectedException(
            LockDetachedException::class
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

    public function testTryAcquireWhenDetached()
    {
        $this->lock->detach();

        $this->setExpectedException(
            LockDetachedException::class
        );

        $this->lock->tryAcquire($this->ttl);
    }

    public function testExtend()
    {
        $this->lock->extend($this->ttl);

        $this
            ->innerLock
            ->extend
            ->calledWith($this->ttl);
    }

    public function testExtendWhenDetached()
    {
        $this->lock->detach();

        $this->setExpectedException(
            LockDetachedException::class
        );

        $this->lock->extend($this->ttl);
    }

    public function testRelease()
    {
        $this->lock->release();

        $this
            ->innerLock
            ->release
            ->once()
            ->called();
    }

    public function testReleaseWhenDetached()
    {
        $this->lock->detach();

        $this->setExpectedException(
            LockDetachedException::class
        );

        $this->lock->release();
    }
}
