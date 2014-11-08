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
        $this->lock->release();
    }

    /**
     * Get the name of this lock.
     *
     * @return string The name of this lock.
     */
    public function name()
    {
        return $this->lock->name();
    }

    /**
     * Check if this lock has been acquired by this process.
     *
     * @return boolean True if this lock is currently acquired by this process.
     */
    public function isAcquired()
    {
        return 0 !== $this->count;
    }

    /**
     * Attempt to acquire this lock and throw an exception if the acquisition
     * is uncessuccessful.
     *
     * @param integer|float|null $timeout How long to wait for lock acquisition, or null to wait forever.
     *
     * @throws LockAcquisitionException if the lock can not be acquired.
     */
    public function acquire($timeout = null)
    {
        if (!$this->count) {
            $this->lock->acquire($timeout);
        }

        ++$this->count;
    }

    /**
     * Attempt to acquire this lock.
     *
     * @param integer|float|null $timeout How long to wait for lock acquisition.
     *
     * @throws boolean True if the lock is acquired; otherwise, false.
     */
    public function tryAcquire($timeout = null)
    {
        if (!$this->count && !$this->lock->tryAcquire($timeout)) {
            return false;
        }

        ++$this->count;

        return true;
    }

    /**
     * Release this lock immediately.
     *
     * A lock must be released the same number of times it has been acquired.
     */
    public function release()
    {
        if (0 === $this->count) {
            throw new LockNotAcquiredException($this->name());
        } elseif (0 === --$this->count) {
            $this->lock->release();
        }
    }

    private $lock;
    private $count;
}
