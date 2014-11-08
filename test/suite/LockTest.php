<?php
namespace Icecave\Chastity;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Chastity\Exception\LockAcquisitionException;
use Icecave\Chastity\Exception\LockAlreadyAcquiredException;
use Icecave\Chastity\Exception\LockNotAcquiredException;
use PHPUnit_Framework_TestCase;

class LockTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->driver = Phony::mock(DriverInterface::class);

        $this
            ->driver
            ->acquire
            ->returns('<token>');

        $this->lock = new Lock(
            $this->driver->mock(),
            'lock-name'
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
        $this->lock->acquire();
        $this->lock->__destruct();

        $this
            ->driver
            ->release
            ->once()
            ->calledWith('lock-name', '<token>');
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
            ->driver
            ->acquire
            ->once()
            ->calledWith('lock-name', null);

        $this->assertTrue(
            $this->lock->isAcquired()
        );
    }

    public function testAcquireWithTimeout()
    {
        $this->lock->acquire(123);

        $this
            ->driver
            ->acquire
            ->once()
            ->calledWith('lock-name', 123);

        $this->assertTrue(
            $this->lock->isAcquired()
        );
    }

    public function testAcquireFailure()
    {
        $this
            ->driver
            ->acquire
            ->returns(null);

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

    public function testAcquireAlreadyAcquiredFailure()
    {
        $this->setExpectedException(
            LockAlreadyAcquiredException::class,
            'Lock has already been acquired: lock-name.'
        );

        $this->lock->acquire();
        $this->lock->acquire();
    }

    public function testTryAcquire()
    {
        $isAcquired = $this->lock->tryAcquire();

        $this
            ->driver
            ->acquire
            ->once()
            ->calledWith('lock-name', null);

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
            ->driver
            ->acquire
            ->once()
            ->calledWith('lock-name', 123);

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
            ->returns(null);

        $this->assertFalse(
            $this->lock->tryAcquire()
        );
    }

    public function testTryAcquireAlreadyAcquiredFailure()
    {
        $this->setExpectedException(
            LockAlreadyAcquiredException::class,
            'Lock has already been acquired: lock-name.'
        );

        $this->lock->tryAcquire();
        $this->lock->tryAcquire();
    }

    public function testRelease()
    {
        $this->lock->tryAcquire();
        $this->lock->release();

        $this
            ->driver
            ->release
            ->once()
            ->calledWith('lock-name', '<token>');

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
}
