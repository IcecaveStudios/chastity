<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Exception\LockDetachedException;
use Icecave\Chastity\Exception\LockException;

/**
 * Automatically releases an underlying lock upon destruction.
 */
class ScopedLock implements ScopedLockInterface
{
    /**
     * @param LockInterface $lock The underlying lock.
     */
    public function __construct(LockInterface $lock)
    {
        $this->lock = $lock;
    }

    /**
     * Release this lock.
     */
    public function __destruct()
    {
        if ($this->lock) {
            $this->lock->release();
        }
    }

    /**
     * Detach this scoped lock from its underlying lock.
     *
     * A detached scoped lock will no longer automatically release the lock on
     * destruction.
     *
     * @return LockInterface         The underlying lock.
     * @throws LockDetachedException if the lock has been detached.
     */
    public function detach()
    {
        if (!$this->lock) {
            throw new LockDetachedException;
        }

        $lock       = $this->lock;
        $this->lock = null;

        return $lock;
    }

    /**
     * Get the resource to which this lock applies.
     *
     * @return string                The resource to which this lock applies.
     * @throws LockDetachedException if the lock has been detached.
     */
    public function resource()
    {
        if (!$this->lock) {
            throw new LockDetachedException;
        }

        return $this->lock->resource();
    }

    /**
     * Attempt to acquire this lock and throw an exception if acquisition
     * is unsuccessful.
     *
     * @param integer|float $ttl     How long the lock should persist, in seconds.
     * @param integer|float $timeout How long to wait for lock acquisition, in seconds.
     *
     * @throws LockException         if the lock can not be acquired.
     * @throws LockDetachedException if the lock has been detached.
     */
    public function acquire($ttl, $timeout = INF)
    {
        if (!$this->lock) {
            throw new LockDetachedException;
        }

        $this->lock->acquire($ttl, $timeout);
    }

    /**
     * Attempt to acquire this lock.
     *
     * @param integer|float $ttl     How long the lock should persist, in seconds.
     * @param integer|float $timeout How long to wait for lock acquisition, in seconds.
     *
     * @return boolean               True if the lock is acquired; otherwise, false.
     * @throws LockDetachedException if the lock has been detached.
     */
    public function tryAcquire($ttl, $timeout = INF)
    {
        if (!$this->lock) {
            throw new LockDetachedException;
        }

        return $this->lock->tryAcquire($ttl, $timeout);
    }

    /**
     * Extend the TTL of this lock.
     *
     * @param integer|float $ttl How long the lock should persist, in seconds.
     *
     * @throws LockNotAcquiredException if the lock has not been acquired.
     * @throws LockDetachedException    if the lock has been detached.
     */
    public function extend($ttl)
    {
        if (!$this->lock) {
            throw new LockDetachedException;
        }

        $this->lock->extend($ttl);
    }

    /**
     * Release this lock.
     *
     * @throws LockDetachedException if the lock has been detached.
     */
    public function release()
    {
        if (!$this->lock) {
            throw new LockDetachedException;
        }

        $this->lock->release();
    }

    private $lock;
}
