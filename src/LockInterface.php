<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Exception\LockException;

interface LockInterface
{
    /**
     * Get the resource to which this lock applies.
     *
     * @return string The resource to which this lock applies.
     */
    public function resource();

    /**
     * Attempt to acquire this lock and throw an exception if acquisition is
     * unsuccessful.
     *
     * @param integer|float $ttl     How long the lock should persist, in seconds.
     * @param integer|float $timeout How long to wait for lock acquisition, in seconds.
     *
     * @throws LockException if the lock can not be acquired.
     */
    public function acquire($ttl, $timeout = INF);

    /**
     * Attempt to acquire this lock.
     *
     * @param integer|float $ttl     How long the lock should persist, in seconds.
     * @param integer|float $timeout How long to wait for lock acquisition, in seconds.
     *
     * @return boolean True if the lock is acquired; otherwise, false.
     */
    public function tryAcquire($ttl, $timeout = INF);

    /**
     * Extend the TTL of this lock.
     *
     * @param integer|float $ttl How long the lock should persist, in seconds.
     *
     * @throws LockException if the lock has not been acquired.
     */
    public function extend($ttl);

    /**
     * Release this lock.
     */
    public function release();
}
