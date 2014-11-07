<?php
namespace Icecave\Chastity;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Chastity\Exception\LockAcquisitionException;
use PHPUnit_Framework_TestCase;

class LockTraitTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->lock = Phony::mock(
            [
                LockInterface::class,
                Redis\RedisLock::class, // LockTrait::class,
            ],
            null
        );

        $this
            ->lock
            ->name
            ->returns('lock-name');

        $this
            ->lock
            ->acquireLogic
            ->returns(true);

        // TODO: Remove this, it's only needed while using the RedisLock because
        // Phony fails mocking the LockTrait directly due to abstract methods ...
        $this
            ->lock
            ->releaseLogic
            ->returns(null);
    }

    public function testDestruct()
    {
        $lock = $this->lock->mock();

        $lock->acquire();
        $lock->__destruct();

        $this->assertFalse(
            $lock->isAcquired()
        );
    }

    public function testAcquire()
    {
        $lock = $this->lock->mock();

        $lock->acquire();

        $this
            ->lock
            ->acquireLogic
            ->once()
            ->calledWith(null);

        $this->assertTrue(
            $lock->isAcquired()
        );
    }

    public function testAcquireWithTimeout()
    {
        $lock = $this->lock->mock();

        $lock->acquire(123);

        $this
            ->lock
            ->acquireLogic
            ->once()
            ->calledWith(123);

        $this->assertTrue(
            $lock->isAcquired()
        );
    }

    public function testAcquireFailure()
    {
        $this
            ->lock
            ->acquireLogic
            ->returns(false);

        $this->setExpectedException(
            LockAcquisitionException::class,
            'Unable to acquire lock: lock-name.'
        );

        $this->lock->mock()->acquire();
    }

    public function testTryAcquire()
    {
        $lock = $this->lock->mock();

        $isAcquired = $lock->tryAcquire();

        $this
            ->lock
            ->acquireLogic
            ->once()
            ->calledWith(null);

        $this->assertTrue(
            $isAcquired
        );

        $this->assertTrue(
            $lock->isAcquired()
        );
    }

    public function testTryAcquireWithTimeout()
    {
        $lock = $this->lock->mock();

        $isAcquired = $lock->tryAcquire(123);

        $this
            ->lock
            ->acquireLogic
            ->once()
            ->calledWith(123);

        $this->assertTrue(
            $isAcquired
        );

        $this->assertTrue(
            $lock->isAcquired()
        );
    }

    public function testTryAcquireFailure()
    {
        $this
            ->lock
            ->acquireLogic
            ->returns(false);

        $this->assertFalse(
            $this->lock->mock()->tryAcquire()
        );
    }

    public function testRelease()
    {
        $lock = $this->lock->mock();

        $lock->tryAcquire();
        $lock->release();

        $this
            ->lock
            ->releaseLogic
            ->once()
            ->called();

        $this->assertFalse(
            $lock->isAcquired()
        );
    }

    public function testReleaseWhenNotAcquired()
    {
        $lock = $this->lock->mock();

        $lock->release();

        $this
            ->lock
            ->releaseLogic
            ->never()
            ->called();

        $this->assertFalse(
            $lock->isAcquired()
        );
    }

    public function testNestedAcquisition()
    {
        $lock = $this->lock->mock();

        // First acquisition ...
        $this->assertTrue(
            $lock->tryAcquire()
        );
        $this->assertTrue(
            $lock->isAcquired()
        );

        $this
            ->lock
            ->acquireLogic
            ->once()
            ->calledWith(null);

        // Second acquisition ...
        $this->assertTrue(
            $lock->tryAcquire()
        );
        $this->assertTrue(
            $lock->isAcquired()
        );

        // Even after second acquisition the lock is only actually acquried
        // once ...
        $this
            ->lock
            ->acquireLogic
            ->once()
            ->called();

        // First release ...
        $lock->release();

        $this
            ->lock
            ->releaseLogic
            ->never()
            ->called();

        $this->assertTrue(
            $lock->isAcquired()
        );

        // Second release ...
        $lock->release();

        $this
            ->lock
            ->releaseLogic
            ->once()
            ->called();

        $this->assertFalse(
            $lock->isAcquired()
        );
    }
}
