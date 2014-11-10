<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Exception\LockAcquisitionException;

interface LockFactoryInterface
{
    /**
     * Create a lock object for the given resource.
     *
     * @param string $resource The resource.
     *
     * @return LockInterface An unacquired lock for the given resource.
     */
    public function create($resource);

    /**
     * Attempt to acquire a lock and throw an exception if acquisition is
     * unsuccessful.
     *
     * This is a convenience method analogous to calling acquire() on the lock
     * returned by a call to $this->create($resource).
     *
     * @param string        $resource The resource to lock.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     * @param integer|float $timeout  How long to wait for lock acquisition, in seconds.
     *
     * @return LockInterface            An acquired lock for the given resource.
     * @throws LockAcquisitionException if the lock can not be acquired.
     */
    public function acquire($resource, $ttl, $timeout = INF);

    /**
     * Attempt to acquire a lock.
     *
     * This is a convenience method analogous to calling tryAcquire() on the
     * lock returned by a call to $this->create($resource).
     *
     * @param string        $resource The resource to lock.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     * @param integer|float $timeout  How long to wait for lock acquisition, in seconds.
     *
     * @return LockInterface|null An acquired lock for the given resource, or null if the lock could not be acquired.
     */
    public function tryAcquire($resource, $ttl, $timeout = INF);
}
