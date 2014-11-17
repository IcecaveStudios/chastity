<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Exception\LockException;

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
     * Get the default TTL to use when acquiring locks.
     *
     * @return integer|float How long the lock should persist, in seconds.
     */
    public function defaultTtl();

    /**
     * Set the default TTL to use when acquiring locks.
     *
     * @param integer|float $ttl How long the lock should persist, in seconds.
     */
    public function setDefaultTtl($ttl);

    /**
     * Attempt to acquire a lock and throw an exception if acquisition is
     * unsuccessful.
     *
     * This is a convenience method analogous to calling acquire() on the lock
     * returned by a call to $this->create($resource).
     *
     * @param string             $resource The resource to lock.
     * @param integer|float|null $ttl      How long the lock should persist, in seconds, or null to use the default.
     * @param integer|float      $timeout  How long to wait for lock acquisition, in seconds.
     *
     * @return ScopedLockInterface An acquired lock for the given resource.
     * @throws LockException       if the lock can not be acquired.
     */
    public function acquire($resource, $ttl = null, $timeout = INF);

    /**
     * Attempt to acquire a lock.
     *
     * This is a convenience method analogous to calling tryAcquire() on the
     * lock returned by a call to $this->create($resource).
     *
     * @param string             $resource The resource to lock.
     * @param integer|float|null $ttl      How long the lock should persist, in seconds, or null to use the default.
     * @param integer|float      $timeout  How long to wait for lock acquisition, in seconds.
     *
     * @return ScopedLockInterface|null An acquired lock for the given resource, or null if the lock could not be acquired.
     */
    public function tryAcquire($resource, $ttl = null, $timeout = INF);
}
