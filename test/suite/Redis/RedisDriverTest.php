<?php
namespace Icecave\Chastity\Redis;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Druid\UuidInterface;
use Icecave\Druid\UuidGeneratorInterface;
use PHPUnit_Framework_TestCase;
use Predis\ClientInterface;

class RedisDriverTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->redisClient = Phony::mock(ClientInterface::class);
        $this->uuidGenerator = Phony::mock(UuidGeneratorInterface::class);
        $this->uuid = Phony::mock(UuidInterface::class);

        $this
            ->uuidGenerator
            ->generate
            ->returns($this->uuid->mock());

        $this
            ->uuid
            ->string
            ->returns('<uuid>');

        $this->driver = new RedisDriver(
            $this->redisClient->mock(),
            $this->uuidGenerator->mock(),
            10
        );
    }

    public function testAcquire()
    {
        $token = $this->driver->acquire('lock-name', null);

        $this
            ->redisClient
            ->set
            ->calledWith(
                'chastity.lock-name',
                '<uuid>',
                'EX',
                10,
                'NX'
            );

        $this->assertSame(
            '<uuid>',
            $token
        );
    }

    // public function testRelease()
    // {

    // }
}
