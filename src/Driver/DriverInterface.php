<?php
namespace Icecave\Chastity\Driver;

use Icecave\Chastity\Driver\Exception\DriverUnavailableException;

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
     * @return integer|float              The actual remaining TTL of the lock, (ie, 0 if the lock could not be acquired).
     * @throws DriverUnavailableException if the driver is not available at the current time.
     */
    public function acquire($resource, $token, $ttl, $timeout);

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
    public function extend($resource, $token, $ttl);

    /**
     * Release a lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token originally passed to acquire().
     *
     * @return boolean                    True if the lock was previously acquired; otherwise, false.
     * @throws DriverUnavailableException if the driver is not available at the current time.
     */
    public function release($resource, $token);
}
