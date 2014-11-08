<?php
namespace Icecave\Chastity\Driver\Redis;

use Icecave\Druid\UuidGeneratorInterface;
use Icecave\Chastity\Driver\DriverInterface;
use InvalidArgumentException;
use Predis\ClientInterface;

class RedisDriver implements DriverInterface
{
    public function __construct(
        ClientInterface $redisClient,
        UuidGeneratorInterface $uuidGenerator
    ) {
        $this->redisClient   = $redisClient;
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * Acquire a lock on the given resource.
     *
     * The return value is an opaque "token" representing the acquired lock.
     *
     * @param string        $resource The resource to lock.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     * @param integer|float $timeout  The maximum time to wait for the lock to be acquired, in seconds.
     *
     * @return string|null The acquisition token, or null if acquisition failed.
     */
    public function acquire($resource, $ttl, $timeout)
    {
        $token = $this
            ->uuidGenerator
            ->generate()
            ->string();

        $result = $this->redisClient->set(
            $this->key($resource),
            $token,
            'PX',
            round($ttl * 1000),
            'NX'
        );

        if ($result) {
            return $token;
        }

        return null;
    }

    /**
     * Check if the given token still represents an acquired lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token representing the acquired lock.
     *
     * @return boolean True if the lock is acquired; otherwise, false.
     */
    public function isAcquired($resource, $token)
    {
        return $token === $this->redisClient->get(
            $this->key($resource)
        );
    }

    /**
     * Extend the TTL of a lock that has already been acquired.
     *
     * @param string        $resource The locked resource.
     * @param string        $token    The token representing the acquired lock.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     *
     * @return boolean True if the lock is acquired and has been extended; otherwise, false.
     */
    public function extend($resource, $token, $ttl)
    {
        if (!$this->extendHash) {
            $this->extendHash = $this->redisClient->script(
                'LOAD',
                file_get_contents(__DIR__ . '/script-extend.lua')
            );
        }

        return $this->redisClient->evalsha(
            $this->extendHash,
            1,
            $this->key($resource),
            $token,
            round($ttl * 1000)
        );
    }

    /**
     * Release a lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token representing the acquired lock.
     *
     * @return boolean True if the lock was previously acquired; otherwise, false.
     */
    public function release($resource, $token)
    {
        if (!$this->releaseHash) {
            $this->releaseHash = $this->redisClient->script(
                'LOAD',
                file_get_contents(__DIR__ . '/script-release.lua')
            );
        }

        return $this->redisClient->evalsha(
            $this->releaseHash,
            1,
            $this->key($resource),
            $token
        );
    }

    private function key($resource)
    {
        return 'chastity:' . $resource;
    }

    private $redisClient;
    private $uuidGenerator;
    private $extendHash;
    private $releaseHash;
}
