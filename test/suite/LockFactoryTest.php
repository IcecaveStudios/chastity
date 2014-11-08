<?php
namespace Icecave\Chastity;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Chastity\Exception\LockAcquisitionException;
use Icecave\Chastity\Exception\LockAlreadyAcquiredException;
use Icecave\Chastity\Exception\LockNotAcquiredException;
use PHPUnit_Framework_TestCase;

class LockFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->driver = Phony::mock(DriverInterface::class);

        $this
            ->driver
            ->acquire
            ->returns('<token>');

        $this->factory = new LockFactory(
            $this->driver->mock()
        );
    }

    public function testCreate()
    {
        $lock = $this->factory->create('lock-name');

        $this->assertEquals(
            new Lock($this->driver->mock(), 'lock-name'),
            $lock
        );

        $this->assertFalse(
            $lock->isAcquired()
        );
    }

    public function testAcquire()
    {
        $lock = $this->factory->acquire('lock-name');

        $this->assertInstanceOf(
            Lock::class,
            $lock
        );

        $this->assertTrue(
            $lock->isAcquired()
        );
    }
}
