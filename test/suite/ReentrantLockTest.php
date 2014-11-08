<?php
namespace Icecave\Chastity;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Chastity\Exception\LockAcquisitionException;
use Icecave\Chastity\Exception\LockNotAcquiredException;
use PHPUnit_Framework_TestCase;

class LockTraitTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->underlyingLock = Phony::mock(LockInterface::class);

        $this
            ->underlyingLock
            ->name
            ->returns('lock-name');

        $this
            ->underlyingLock
            ->tryAcquire
            ->returns(true);

        $this->lock = new ReentrantLock(
            $this->underlyingLock->mock()
        );
    }

    public function testDestruct()
    {
        $this->lock->acquire();
        $this->lock->__destruct();

        $this
            ->underlyingLock
            ->release
            ->once()
            ->called();
    }

    public function testName()
    {
        $this->assertSame(
            'lock-name',
            $this->lock->name()
        );
    }

    public function testAcquire()
    {
        $this->lock->acquire();

        $this
            ->underlyingLock
            ->acquire
            ->once()
            ->calledWith(null);

        $this->assertTrue(
            $this->lock->isAcquired()
        );
    }

    public function testAcquireWithTimeout()
    {
        $this->lock->acquire(123);

        $this
            ->underlyingLock
            ->acquire
            ->once()
            ->calledWith(123);

        $this->assertTrue(
            $this->lock->isAcquired()
        );
    }

    public function testAcquireFailure()
    {
        $this
            ->underlyingLock
            ->acquire
            ->throws(
                new LockAcquisitionException('lock-name')
            );

        $this->setExpectedException(
            LockAcquisitionException::class,
            'Unable to acquire lock: lock-name.'
        );

        try {
            $this->lock->acquire();
        } catch (LockAcquisitionException $e) {
            $this->assertFalse(
                $this->lock->isAcquired()
            );

            throw $e;
        }
    }

    public function testTryAcquire()
    {
        $isAcquired = $this->lock->tryAcquire();

        $this
            ->underlyingLock
            ->tryAcquire
            ->once()
            ->calledWith(null);

        $this->assertTrue(
            $isAcquired
        );

        $this->assertTrue(
            $this->lock->isAcquired()
        );
    }

    public function testTryAcquireWithTimeout()
    {
        $isAcquired = $this->lock->tryAcquire(123);

        $this
            ->underlyingLock
            ->tryAcquire
            ->once()
            ->calledWith(123);

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
            ->underlyingLock
            ->tryAcquire
            ->returns(false);

        $this->assertFalse(
            $this->lock->tryAcquire()
        );
    }

    public function testRelease()
    {
        $this->lock->tryAcquire();
        $this->lock->release();

        $this
            ->underlyingLock
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
            'Lock has not been acquired: lock-name.'
        );

        $this->lock->release();
    }

    public function testNestedAcquisition()
    {
        // First acquisition ...
        $this->assertTrue(
            $this->lock->tryAcquire()
        );
        $this->assertTrue(
            $this->lock->isAcquired()
        );

        $this
            ->underlyingLock
            ->tryAcquire
            ->once()
            ->calledWith(null);

        // Second acquisition ...
        $this->assertTrue(
            $this->lock->tryAcquire()
        );
        $this->assertTrue(
            $this->lock->isAcquired()
        );

        // Third acquisition ...
        // Note use of acquire() instead of tryAcquire(), they must be able to be mixed in this manner.
        $this->lock->acquire();

        $this->assertTrue(
            $this->lock->isAcquired()
        );

        // Even after third acquisition the lock is only actually acquried
        // once ...
        $this
            ->underlyingLock
            ->tryAcquire
            ->once()
            ->called();

        $this
            ->underlyingLock
            ->acquire
            ->never()
            ->called();

        // First release ...
        $this->lock->release();

        $this
            ->underlyingLock
            ->release
            ->never()
            ->called();

        $this->assertTrue(
            $this->lock->isAcquired()
        );

        // Second release ...
        $this->lock->release();

        $this
            ->underlyingLock
            ->release
            ->never()
            ->called();

        $this->assertTrue(
            $this->lock->isAcquired()
        );

        // Third release ...
        $this->lock->release();

        $this
            ->underlyingLock
            ->release
            ->once()
            ->called();

        $this->assertFalse(
            $this->lock->isAcquired()
        );
    }
}
