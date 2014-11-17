<?php
namespace Icecave\Chastity\Driver\Redis;

use Icecave\Chastity\Driver\DriverInterface;
use Icecave\Chastity\Driver\Exception\DriverUnavailableException;
use Icecave\Chastity\Driver\PollingDriverTrait;
use Predis\ClientInterface;
use Predis\CommunicationException;

class RedisDriver implements DriverInterface
{
    use PollingDriverTrait;

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
     * @return boolean                    True if the lock is acquired; otherwise, false.
     * @throws DriverUnavailableException if the driver is not available at the current time.
     */
    public function poll($resource, $token, $ttl)
    {
        try {
            return (bool) $this->redisClient->set(
                $this->generateKey($resource),
                $token,
                'PX',
                max(1, intval($ttl * 1000)),
                'NX'
            );
        } catch (CommunicationException $e) {
            throw new DriverUnavailableException($e);
        }
    }

    /**
     * Extend the TTL of a lock that has already been acquired.
     *
     * @param string        $resource The locked resource.
     * @param string        $token    The token originally passed to acquire().
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     *
     * @return integer|float              The actual remaining TTL of the lock, (ie, 0 if the lock could not be extended).
     * @throws DriverUnavailableException if the driver is not available at the current time.
     */
    public function extend($resource, $token, $ttl)
    {
        try {
            if (!$this->extendHash) {
                $this->extendHash = $this->redisClient->script(
                    'LOAD',
                    file_get_contents(__DIR__ . '/redis-extend.lua')
                );
            }

            $ttl = $this->redisClient->evalsha(
                $this->extendHash,
                1,
                $this->generateKey($resource),
                $token,
                max(1, intval($ttl * 1000))
            );

            // Convert micros to seconds ...
            return $ttl / 1000;
        } catch (CommunicationException $e) {
            throw new DriverUnavailableException($e);
        }
    }

    /**
     * Release a lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token originally passed to acquire().
     *
     * @return boolean                    True if the lock was previously acquired; otherwise, false.
     * @throws DriverUnavailableException if the driver is not available at the current time.
     */
    public function release($resource, $token)
    {
        try {
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
        } catch (CommunicationException $e) {
            throw new DriverUnavailableException($e);
        }
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

    private $redisClient;
    private $extendHash;
    private $releaseHash;
}
