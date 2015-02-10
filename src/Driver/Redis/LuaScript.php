<?php
namespace Icecave\Chastity\Driver\Redis;

use Predis\ClientInterface;
use Predis\Response\ServerException;

/**
 * @access private
 *
 * Loads (and re-loads) a LUA script before use.
 *
 * This is used instead of a Predis ScriptCommand so that the Redis driver can
 * operate on any Predis connection without it having to be preconfigured to use
 * Chastity LUA scripts in its profile.
 */
class LuaScript implements LuaScriptInterface
{
    /**
     * @param ClientInterface $redisClient The connection to the Redis server.
     * @param string          $filename    The path to the LUA script.
     * @param integer         $keyCount    The number of arguments that are keys.
     */
    public function __construct(ClientInterface $redisClient, $filename, $keyCount)
    {
        $this->redisClient = $redisClient;
        $this->filename    = $filename;
        $this->keyCount    = $keyCount;
    }

    /**
     * Invoke the LUA script.
     *
     * @param mixed $argument,... The arguments to pass to the script.
     *
     * @return mixed The script result.
     */
    public function invoke()
    {
        return $this->invokeArray(
            func_get_args()
        );
    }

    /**
     * Invoke the LUA script.
     *
     * @param array $arguments The arguments to pass to the script.
     *
     * @return mixed The script result.
     */
    public function invokeArray(array $arguments)
    {
        // Build the command to execute ...
        $command = $this->redisClient->createCommand(
            'EVALSHA',
            array_merge(
                [
                    $this->hash(),
                    $this->keyCount,
                ],
                $arguments
            )
        );

        // Attempt to execute the command ...
        try {
            return $this
                ->redisClient
                ->executeCommand($command);

        // If the command failed because the script is not loaded, load it and
        // then try again ...
        } catch (ServerException $e) {
            if ('NOSCRIPT' !== $e->getErrorType()) {
                throw $e;
            }

            $this->load();

            return $this
                ->redisClient
                ->executeCommand($command);
        }
    }

    /**
     * Get the SHA1 hash of the LUA source.
     *
     * @return string
     */
    public function hash()
    {
        if (null === $this->hash) {
            $this->hash = sha1_file($this->filename);
        }

        return $this->hash;
    }

    /**
     * Load the script immediately.
     */
    public function load()
    {
        $this->hash = $this->redisClient->script(
            'LOAD',
            file_get_contents($this->filename)
        );
    }

    private $redisClient;
    private $filename;
    private $keyCount;
    private $hash;
}
