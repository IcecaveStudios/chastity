<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Exception\LockAcquisitionException;
use Icecave\Chastity\Exception\LockAlreadyAcquiredException;
use Icecave\Chastity\Exception\LockNotAcquiredException;

interface LockInterface
{
    /**
     * Get the resource to which this lock applies.
     *
     * @return string The resource to which this lock applies.
     */
    public function resource();

    /**
     * Check if this lock has been acquired.
     *
     * @return boolean True if this lock is currently acquired by this process.
     */
    public function isAcquired();

    /**
     * Attempt to acquire this lock and throw an exception if acquisition is
     * unsuccessful.
     *
     * @param integer|float $ttl     How long the lock should persist, in seconds.
     * @param integer|float $timeout How long to wait for lock acquisition, in seconds.
     *
     * @throws LockAcquisitionException     if the lock can not be acquired.
     * @throws LockAlreadyAcquiredException if the lock is already acquired.
     */
    public function acquire($ttl, $timeout = INF);

    /**
     * Attempt to acquire this lock.
     *
     * @param integer|float $ttl     How long the lock should persist, in seconds.
     * @param integer|float $timeout How long to wait for lock acquisition, in seconds.
     *
     * @return boolean                      True if the lock is acquired; otherwise, false.
     * @throws LockAlreadyAcquiredException if the lock is already acquired.
     */
    public function tryAcquire($ttl, $timeout = INF);

    /**
     * Extend the TTL of this lock.
     *
     * @param integer|float $ttl How long the lock should persist, in seconds.
     *
     * @throws LockNotAcquiredException if the lock has not been acquired.
     */
    public function extend($ttl);

    /**
     * Release this lock.
     *
     * @throws LockNotAcquiredException if the lock has not been acquired.
     */
    public function release();
}
