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
     * The return value is an opaque "token" representing the acquired lock.
     *
     * @param string        $resource The resource to lock.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     * @param integer|float $timeout  The maximum time to wait for the lock to be acquired, in seconds.
     *
     * @return string|null The acquisition token, or null if acquisition failed.
     */
    public function acquire($resource, $ttl, $timeout);

    /**
     * Check if the given token still represents an acquired lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token representing the acquired lock.
     *
     * @return boolean True if the lock is acquired; otherwise, false.
     */
    public function isAcquired($resource, $token);

    /**
     * Extend the TTL of a lock that has already been acquired.
     *
     * @param string        $resource The locked resource.
     * @param string        $token    The token representing the acquired lock.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     *
     * @return boolean True if the lock is acquired and has been extended; otherwise, false.
     */
    public function extend($resource, $token, $ttl);

    /**
     * Release a lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token representing the acquired lock.
     *
     * @return boolean True if the lock was previously acquired; otherwise, false.
     */
    public function release($resource, $token);
}
