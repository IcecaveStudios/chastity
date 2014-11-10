<?php
namespace Icecave\Chastity;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Chastity\Driver\BlockingAdaptor;
use Icecave\Chastity\Driver\BlockingDriverInterface;
use Icecave\Chastity\Driver\DriverInterface;
use Icecave\Druid\Uuid;
use Icecave\Druid\UuidGeneratorInterface;
use Icecave\Druid\UuidInterface;
use PHPUnit_Framework_TestCase;

class LockFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->driver = Phony::mock(BlockingDriverInterface::class);
        $this->uuidGenerator = Phony::mock(UuidGeneratorInterface::class);
        $this->uuid = Phony::mock(UuidInterface::class);

        $this
            ->uuidGenerator
            ->generate
            ->returns($this->uuid->mock());

        $this
            ->uuid
            ->string
            ->returns('<token>');

        $this
            ->driver
            ->acquire
            ->returns(true);

        $this
            ->driver
            ->isAcquired
            ->returns(true);

        $this->ttl = 10;
        $this->timeout = 30;

        $this->factory = new LockFactory(
            $this->driver->mock(),
            $this->uuidGenerator->mock()
        );
    }

    public function testConstructorDefaults()
    {
        $this->factory = new LockFactory(
            $this->driver->mock()
        );

        $lock = $this->factory->create('<resource>');

        $this->assertInstanceOf(
            Lock::class,
            $lock
        );

        $lock->acquire($this->ttl);

        $token = $this
            ->driver
            ->acquire
            ->called()
            ->argument(1);

        // Allow to throw if the token was not a UUID ...
        Uuid::fromString($token);
    }

    public function testBlockingAdaptor()
    {
        $this->driver = Phony::mock(DriverInterface::class);

        $this->factory = new LockFactory(
            $this->driver->mock(),
            $this->uuidGenerator->mock()
        );

        $lock = $this->factory->create('<resource>');

        $this->assertEquals(
            new Lock(
                new BlockingAdaptor($this->driver->mock()),
                '<resource>',
                '<token>'
            ),
            $lock
        );
    }

    public function testCreate()
    {
        $lock = $this->factory->create('<resource>');

        $this->assertEquals(
            new Lock(
                $this->driver->mock(),
                '<resource>',
                '<token>'
            ),
            $lock
        );

        $this->assertFalse(
            $lock->isAcquired()
        );
    }

    public function testAcquire()
    {
        $lock = $this->factory->acquire('<resource>', $this->ttl);

        $this->assertInstanceOf(
            Lock::class,
            $lock
        );

        $this->assertTrue(
            $lock->isAcquired()
        );

        $this
            ->driver
            ->acquire
            ->calledWith(
                '<resource>',
                '<token>',
                $this->ttl,
                INF
            );
    }

    public function testAcquireWithTimeout()
    {
        $lock = $this->factory->acquire('<resource>', $this->ttl, $this->timeout);

        $this->assertInstanceOf(
            Lock::class,
            $lock
        );

        $this->assertTrue(
            $lock->isAcquired()
        );

        $this
            ->driver
            ->acquire
            ->calledWith(
                '<resource>',
                '<token>',
                $this->ttl,
                $this->timeout
            );
    }

    public function testTryAcquire()
    {
        $lock = $this->factory->tryAcquire('<resource>', $this->ttl);

        $this->assertInstanceOf(
            Lock::class,
            $lock
        );

        $this->assertTrue(
            $lock->isAcquired()
        );

        $this
            ->driver
            ->acquire
            ->calledWith(
                '<resource>',
                '<token>',
                $this->ttl,
                INF
            );
    }

    public function testTryAcquireWithTimeout()
    {
        $lock = $this->factory->tryAcquire('<resource>', $this->ttl, $this->timeout);

        $this->assertInstanceOf(
            Lock::class,
            $lock
        );

        $this->assertTrue(
            $lock->isAcquired()
        );

        $this
            ->driver
            ->acquire
            ->calledWith(
                '<resource>',
                '<token>',
                $this->ttl,
                $this->timeout
            );
    }

    public function testTryAcquireFailure()
    {
        $this
            ->driver
            ->acquire
            ->returns(false);

        $lock = $this->factory->tryAcquire('<resource>', $this->ttl);

        $this->assertNull(
            $lock
        );

        $this
            ->driver
            ->acquire
            ->calledWith(
                '<resource>',
                '<token>',
                $this->ttl,
                INF
            );
    }
}