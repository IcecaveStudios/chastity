<?php
namespace Icecave\Chastity\Driver\Redis;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Chastity\Driver\Exception\DriverUnavailableException;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Predis\ClientInterface;
use Predis\CommunicationException;

class RedisDriverTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->redisClient            = Phony::mock(ClientInterface::class);
        $this->communicationException = Phony::fullMock(CommunicationException::class)->mock();

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

        $this->extendScript  = __DIR__ . '/../../../../src/Driver/Redis/redis-extend.lua';
        $this->releaseScript = __DIR__ . '/../../../../src/Driver/Redis/redis-release.lua';

        $this->driver = new RedisDriver(
            $this->redisClient->mock()
        );
    }

    public function testPoll()
    {
        $result = $this->driver->poll(
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

    public function testPollFailure()
    {
        $this
            ->redisClient
            ->set
            ->returns(false);

        $result = $this->driver->poll(
            '<resource>',
            '<token>',
            1.5
        );

        $this->assertFalse(
            $result
        );
    }

    public function testPollWithCommunicationException()
    {
        $this
            ->redisClient
            ->set
            ->throws($this->communicationException);

        $this->setExpectedException(
            DriverUnavailableException::class
        );

        $result = $this->driver->poll(
            '<resource>',
            '<token>',
            1.5
        );
    }

    public function testPollWithInvalidTtl()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'TTL must be greater than zero.'
        );

        $this->driver->poll(
            '<resource>',
            '<token>',
            0
        );
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

    public function testExtendWithCommunicationException()
    {
        $this
            ->redisClient
            ->evalsha
            ->throws($this->communicationException);

        $this->setExpectedException(
            DriverUnavailableException::class
        );

        $result = $this->driver->extend(
            '<resource>',
            '<token>',
            1
        );
    }

    public function testRelease()
    {
        $this->driver->release('<resource>', '<token>');

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

    public function testReleaseWithCommunicationException()
    {
        $this
            ->redisClient
            ->evalsha
            ->throws($this->communicationException);

        $this->setExpectedException(
            DriverUnavailableException::class
        );

        $result = $this->driver->release(
            '<resource>',
            '<token>'
        );
    }
}
