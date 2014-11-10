<?php
namespace Icecave\Chastity\Driver;

/**
 * A low-level lock implementation that supports blocking lock acquisition.
 */
interface BlockingDriverInterface extends DriverInterface
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
    public function acquire($resource, $token, $ttl, $timeout = 0);
}
