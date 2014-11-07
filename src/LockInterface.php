<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Exception\LockAcquisitionException;

interface LockInterface
{
    /**
     * Release this lock.
     */
    public function __destruct();

    /**
     * Get the name of this lock.
     *
     * @return string The name of this lock.
     */
    public function name();

    /**
     * Check if this lock has been acquired by this process.
     *
     * @return boolean True if this lock is currently acquired by this process.
     */
    public function isAcquired();

    /**
     * Attempt to acquire this lock and throw an exception if the acquisition
     * is uncessuccessful.
     *
     * @param integer|float|null $timeout How long to wait for lock acquisition.
     *
     * @throws LockAcquisitionException if the lock can not be acquired.
     */
    public function acquire($timeout = null);

    /**
     * Attempt to acquire this lock.
     *
     * @param integer|float|null $timeout How long to wait for lock acquisition.
     *
     * @throws boolean True if the lock is acquired; otherwise, false.
     */
    public function tryAcquire($timeout = null);

    /**
     * Release this lock.
     *
     * A lock must be released the same number of times it has been acquired.
     */
    public function release();
}
