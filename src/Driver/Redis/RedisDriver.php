<?php
namespace Icecave\Chastity\Driver\Redis;

use Icecave\Chastity\Driver\DriverInterface;
use InvalidArgumentException;
use Predis\ClientInterface;

class RedisDriver implements DriverInterface
{
    /**
     * @param ClientInterface $redisClient The connection to the Redis server.
     */
    public function __construct(ClientInterface $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    /**
     * Acquire a lock on the given resource.
     *
     * @param string        $resource The resource to lock.
     * @param string        $token    The unique token representing the acquisition request.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     *
     * @return boolean True if the lock is acquired; otherwise, false.
     */
    public function acquire($resource, $token, $ttl)
    {
        return (bool) $this->redisClient->set(
            $this->generateKey($resource),
            $token,
            'PX',
            $this->convertTimeToLive($ttl),
            'NX'
        );
    }

    /**
     * Check if the given token still represents an acquired lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token originally passed to acquire().
     *
     * @return boolean True if the lock is acquired; otherwise, false.
     */
    public function isAcquired($resource, $token)
    {
        return $token === $this->redisClient->get(
            $this->generateKey($resource)
        );
    }

    /**
     * Extend the TTL of a lock that has already been acquired.
     *
     * @param string        $resource The locked resource.
     * @param string        $token    The token originally passed to acquire().
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     *
     * @return boolean True if the lock is acquired and has been extended; otherwise, false.
     */
    public function extend($resource, $token, $ttl)
    {
        if (!$this->extendHash) {
            $this->extendHash = $this->redisClient->script(
                'LOAD',
                file_get_contents(__DIR__ . '/redis-extend.lua')
            );
        }

        return (bool) $this->redisClient->evalsha(
            $this->extendHash,
            1,
            $this->generateKey($resource),
            $token,
            $this->convertTimeToLive($ttl)
        );
    }

    /**
     * Release a lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token originally passed to acquire().
     *
     * @return boolean True if the lock was previously acquired; otherwise, false.
     */
    public function release($resource, $token)
    {
        if (!$this->releaseHash) {
            $this->releaseHash = $this->redisClient->script(
                'LOAD',
                file_get_contents(__DIR__ . '/redis-release.lua')
            );
        }

        return (bool) $this->redisClient->evalsha(
            $this->releaseHash,
            1,
            $this->generateKey($resource),
            $token
        );
    }

    /**
     * Generate the Redis key to use as the lock primitive for the given
     * resource.
     *
     * @param string $resource
     *
     * @return string
     */
    private function generateKey($resource)
    {
        return 'chastity:' . $resource;
    }

    /**
     * Convert a TTL in seconds to microseconds.
     *
     * @param integer|float $ttl
     *
     * @return integer
     */
    private function convertTimeToLive($ttl)
    {
        $ttl = intval($ttl * 1000);

        if ($ttl <= 0) {
            throw new InvalidArgumentException('TTL must be greater than zero.');
        }

        return $ttl;
    }

    private $redisClient;
    private $extendHash;
    private $releaseHash;
}
