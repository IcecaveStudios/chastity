<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Exception\LockAcquisitionException;

trait LockTrait
{
    /**
     * Release this lock.
     */
    public function __destruct()
    {
        while ($this->isAcquired()) {
            $this->release();
        }
    }

    /**
     * Get the name of this lock.
     *
     * @return string The name of this lock.
     */
    public abstract function name();

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
     * @param integer|float|null $timeout How long to wait for lock acquisition.
     *
     * @throws LockAcquisitionException if the lock can not be acquired.
     */
    public function acquire($timeout = null)
    {
        if ($this->tryAcquire($timeout)) {
            return;
        }

        throw new LockAcquisitionException(
            $this->name()
        );
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
        // Lock is already acquired ...
        if ($this->count) {
            ++$this->count;

        // Lock is successfully required for the first time ...
        } elseif ($this->acquireLogic($timeout)) {
            ++$this->count;
        }

        return $this->isAcquired();
    }

    /**
     * Release this lock immediately.
     *
     * A lock must be released the same number of times it has been acquired.
     */
    public function release()
    {
        // Lock is already released ...
        if (0 === $this->count) {
            return;

        // Lock is still acquired ...
        } elseif (--$this->count) {
            return;
        }

        // Lock is actually released ...
        $this->releaseLogic();
    }

    /**
     * Acquire this lock.
     *
     * @param integer|float|null $timeout
     *
     * @return boolean
     */
    protected abstract function acquireLogic($timeout);

    /**
     * Release this lock.
     */
    protected abstract function releaseLogic();

    private $count = 0;
}
