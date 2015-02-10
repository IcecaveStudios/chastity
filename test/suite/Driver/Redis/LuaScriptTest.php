<?php
namespace Icecave\Chastity\Driver\Redis;

use Eloquent\Phony\Phpunit\Phony;
use PHPUnit_Framework_TestCase;
use Predis\ClientInterface;
use Predis\Command\CommandInterface;
use Predis\Response\ServerException;

class LuaScriptTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->redisClient = Phony::mock(ClientInterface::class);
        $this->command     = Phony::mock(CommandInterface::class);
        $this->filename    = __DIR__ . '/../../../../src/Driver/Redis/redis-extend.lua';
        $this->hash        = sha1_file($this->filename);

        $this
            ->redisClient
            ->createCommand
            ->returns(
                $this->command->mock()
            );

        $this
            ->redisClient
            ->script
            ->returns($this->hash);

        $this
            ->redisClient
            ->executeCommand
            ->returns('<result>');

        $this->script = new LuaScript(
            $this->redisClient->mock(),
            $this->filename,
            1
        );
    }

    public function testInvoke()
    {
        $result = $this
            ->script
            ->invoke('a', 'b', 'c');

        $this
            ->redisClient
            ->createCommand
            ->calledWith(
                'EVALSHA',
                [
                    $this->hash,
                    1,
                    'a',
                    'b',
                    'c',
                ]
            );

        $this->assertEquals(
            '<result>',
            $result
        );
    }

    public function testInvokeArray()
    {
        $result = $this
            ->script
            ->invokeArray(
                ['a', 'b', 'c']
            );

        $this
            ->redisClient
            ->createCommand
            ->calledWith(
                'EVALSHA',
                [
                    $this->hash,
                    1,
                    'a',
                    'b',
                    'c',
                ]
            );

        $this
            ->redisClient
            ->executeCommand
            ->calledWith(
                $this->command->mock()
            );

        $this->assertEquals(
            '<result>',
            $result
        );
    }

    public function testInvokeArrayWithUnknownScript()
    {
        $exception = new ServerException('NOSCRIPT No matching script. Please use EVAL.');

        $this
            ->redisClient
            ->executeCommand
            ->throws($exception)
            ->returns('<result>');

        $result = $this
            ->script
            ->invokeArray([]);

        $executeVerifier = $this
            ->redisClient
            ->executeCommand
            ->twice()
            ->calledWith(
                $this->command->mock()
            );

        Phony::inOrder(
            $executeVerifier,
            $this
                ->redisClient
                ->script
                ->calledWith(
                    'LOAD',
                    file_get_contents($this->filename)
                ),
            $executeVerifier
        );

        $this->assertEquals(
            '<result>',
            $result
        );
    }

    public function testInvokeArrayWithOtherServerException()
    {
        $exception = new ServerException('ERR wrong number of arguments');

        $this
            ->redisClient
            ->executeCommand
            ->throws($exception);

        $this->setExpectedException(
            ServerException::class,
            'ERR wrong number of arguments'
        );

        try {
            $this
                ->script
                ->invoke();
        } catch (ServerException $e) {
            $this
                ->redisClient
                ->script
                ->never()
                ->called();

            throw $e;
        }
    }
}
