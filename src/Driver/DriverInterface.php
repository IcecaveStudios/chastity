<?php
namespace Icecave\Chastity\Driver;

/**
 * A low-level lock implementation.
 */
interface DriverInterface
{
    /**
     * Acquire a lock on the given resource.
     *
     * @param string        $resource The resource to lock.
     * @param string        $token    The unique token representing the acquisition request.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     * @param integer|float $timeout  How long to wait for the lock to be acquired, in seconds.
     *
     * @return boolean True if the lock is acquired; otherwise, false.
     */
    public function acquire($resource, $token, $ttl, $timeout);

    /**
     * Check if the given token still represents an acquired lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token originally passed to acquire().
     *
     * @return boolean True if the lock is acquired; otherwise, false.
     */
    public function isAcquired($resource, $token);

    /**
     * Extend the TTL of a lock that has already been acquired.
     *
     * @param string        $resource The locked resource.
     * @param string        $token    The token originally passed to acquire().
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     *
     * @return boolean True if the lock is acquired and has been extended; otherwise, false.
     */
    public function extend($resource, $token, $ttl);

    /**
     * Release a lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token originally passed to acquire().
     *
     * @return boolean True if the lock was previously acquired; otherwise, false.
     */
    public function release($resource, $token);
}
