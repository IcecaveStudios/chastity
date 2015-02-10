<?php
namespace Icecave\Chastity\Driver\Redis;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Chastity\Driver\Exception\DriverUnavailableException;
use PHPUnit_Framework_TestCase;
use Predis\ClientInterface;
use Predis\CommunicationException;

class RedisDriverTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->redisClient            = Phony::mock(ClientInterface::class);
        $this->extendScript           = Phony::mock(LuaScriptInterface::class);
        $this->releaseScript          = Phony::mock(LuaScriptInterface::class);
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

        $this->driver = new RedisDriver(
            $this->redisClient->mock(),
            $this->extendScript->mock(),
            $this->releaseScript->mock()
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

    public function testExtend()
    {
        $this
            ->extendScript
            ->invoke
            ->returns(1500);

        $result = $this->driver->extend('<resource>', '<token>', 1.5);

        $this
            ->extendScript
            ->invoke
            ->calledWith(
                'chastity:<resource>',
                '<token>',
                1500
            );

        $this->assertSame(
            1.5,
            $result
        );
    }

    public function testExtendWithCommunicationException()
    {
        $this
            ->extendScript
            ->invoke
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
        $this
            ->releaseScript
            ->invoke
            ->returns(true);

        $result = $this->driver->release('<resource>', '<token>');

        $this
            ->releaseScript
            ->invoke
            ->calledWith(
                'chastity:<resource>',
                '<token>'
            );

        $this->assertTrue(
            $result
        );
    }

    public function testReleaseFailure()
    {
        $this
            ->releaseScript
            ->invoke
            ->returns(false);

        $this->assertFalse(
            $this->driver->release('<resource>', '<token>')
        );
    }

    public function testReleaseWithCommunicationException()
    {
        $this
            ->releaseScript
            ->invoke
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
