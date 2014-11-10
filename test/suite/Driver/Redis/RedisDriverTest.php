<?php
namespace Icecave\Chastity\Driver\Redis;

use Eloquent\Phony\Phpunit\Phony;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Predis\ClientInterface;

class RedisDriverTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->redisClient = Phony::mock(ClientInterface::class);

        $this
            ->redisClient
            ->set
            ->returns(true);

        $this
            ->redisClient
            ->get
            ->returns('<token>');

        $this
            ->redisClient
            ->script
            ->with('LOAD', '*')
            ->does(
                function ($subCommand, $script) {
                    return sha1($script);
                }
            );

        $this
            ->redisClient
            ->evalsha
            ->returns(true);

        $this->extendScript = __DIR__ . '/../../../../src/Driver/Redis/redis-extend.lua';
        $this->releaseScript = __DIR__ . '/../../../../src/Driver/Redis/redis-release.lua';

        $this->driver = new RedisDriver(
            $this->redisClient->mock()
        );
    }

    public function testAcquire()
    {
        $result = $this->driver->acquire(
            '<resource>',
            '<token>',
            1.5
        );

        $this
            ->redisClient
            ->set
            ->calledWith(
                'chastity:<resource>',
                '<token>',
                'PX',
                1500,
                'NX'
            );

        $this->assertTrue(
            $result
        );
    }

    public function testAcquireFailure()
    {
        $this
            ->redisClient
            ->set
            ->returns(false);

        $result = $this->driver->acquire(
            '<resource>',
            '<token>',
            1.5
        );

        $this->assertFalse(
            $result
        );
    }

    public function testAcquireWithInvalidTtl()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'TTL must be greater than zero.'
        );

        $this->driver->acquire(
            '<resource>',
            '<token>',
            0
        );
    }

    public function testIsAcquired()
    {
        $this->assertTrue(
            $this->driver->isAcquired('<resource>', '<token>')
        );

        $this
            ->redisClient
            ->get
            ->calledWith('chastity:<resource>');
    }

    public function testIsAcquiredFailure()
    {
        $this->assertFalse(
            $this->driver->isAcquired('<resource>', '<different-token>')
        );

        $this
            ->redisClient
            ->get
            ->calledWith('chastity:<resource>');
    }

    public function testExtend()
    {
        $result = $this->driver->extend('<resource>', '<token>', 1.5);

        Phony::inOrder(
            $this
                ->redisClient
                ->script
                ->calledWith(
                    'LOAD',
                    file_get_contents($this->extendScript)
                ),
            $this
                ->redisClient
                ->evalsha
                ->calledWith(
                    sha1_file($this->extendScript),
                    1,
                    'chastity:<resource>',
                    '<token>',
                    1500
                )
        );

        $this->assertTrue(
            $result
        );
    }

    public function testExtendOnlyLoadsScriptOnce()
    {
        $this->driver->extend('<resource>', '<token>', 1.5);
        $this->driver->extend('<resource>', '<token>', 1.5);

        $this
            ->redisClient
            ->script
            ->once()
            ->called();
    }

    public function testExtendWithInvalidTtl()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'TTL must be greater than zero.'
        );

        $this->driver->extend(
            '<resource>',
            '<token>',
            0
        );
    }

    public function testRelease()
    {
        $result = $this->driver->release('<resource>', '<token>');

        Phony::inOrder(
            $this
                ->redisClient
                ->script
                ->calledWith(
                    'LOAD',
                    file_get_contents($this->releaseScript)
                ),
            $this
                ->redisClient
                ->evalsha
                ->calledWith(
                    sha1_file($this->releaseScript),
                    1,
                    'chastity:<resource>',
                    '<token>'
                )
        );

        $this->assertTrue(
            $result
        );
    }

    public function testReleaseOnlyLoadsScriptOnce()
    {
        $this->driver->release('<resource>', '<token>');
        $this->driver->release('<resource>', '<token>');

        $this
            ->redisClient
            ->script
            ->once()
            ->called();
    }
}
