<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Exception\LockAcquisitionException;
use Icecave\Chastity\Exception\LockNotAcquiredException;

/**
 * Wraps an existing lock to provide reentrancy support.
 */
class ReentrantLock implements LockInterface
{
    /**
     * @param LockInterface $lock The underlying lock.
     */
    public function __construct(LockInterface $lock)
    {
        $this->lock = $lock;
        $this->count = 0;
    }

    /**
     * Release this lock.
     */
    public function __destruct()
    {
        try {
            $this->lock->release();
        } catch (LockNotAcquiredException $e) {
            // ignore ...
        }
    }

    /**
     * Get the resource to which this lock applies.
     *
     * @return string The resource to which this lock applies.
     */
    public function resource()
    {
        return $this->lock->resource();
    }

    /**
     * Check if this lock has been acquired.
     *
     * @return boolean True if this lock is currently acquired by this process.
     */
    public function isAcquired()
    {
        if (!$this->count) {
            return false;
        } elseif ($this->lock->isAcquired()) {
            return true;
        }

        $this->count = 0;

        return false;
    }

    /**
     * Attempt to acquire this lock and throw an exception if acquisition
     * is unsuccessful.
     *
     * @param integer|float $ttl     How long the lock should persist, in seconds.
     * @param integer|float $timeout How long to wait for lock acquisition, in seconds.
     *
     * @throws LockAcquisitionException     if the lock can not be acquired.
     * @throws LockAlreadyAcquiredException if the lock is already acquired.
     */
    public function acquire($ttl, $timeout = INF)
    {
        if (!$this->count) {
            $this->lock->acquire($ttl, $timeout);
        }

        ++$this->count;
    }

    /**
     * Attempt to acquire this lock.
     *
     * @param integer|float $ttl     How long the lock should persist, in seconds.
     * @param integer|float $timeout How long to wait for lock acquisition, in seconds.
     *
     * @return boolean                      True if the lock is acquired; otherwise, false.
     * @throws LockAlreadyAcquiredException if the lock is already acquired.
     */
    public function tryAcquire($ttl, $timeout = INF)
    {
        if (
            !$this->count
            && !$this->lock->tryAcquire($ttl, $timeout)
        ) {
            return false;
        }

        ++$this->count;

        return true;
    }

    /**
     * Extend the TTL of this lock.
     *
     * @param integer|float $ttl How long the lock should persist, in seconds.
     *
     * @return boolean                  True if the lock is acquired and has been extended; otherwise, false.
     * @throws LockNotAcquiredException if the lock has not been acquired.
     */
    public function extend($ttl)
    {
        $this->lock->extend($ttl);
    }

    /**
     * Release this lock.
     *
     * @throws LockNotAcquiredException if the lock has not been acquired.
     */
    public function release()
    {
        if (0 === $this->count) {
            throw new LockNotAcquiredException($this->lock->resource());
        } elseif (0 === --$this->count) {
            $this->lock->release();
        }
    }

    private $lock;
    private $count;
}
