<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Driver\BlockingDriverInterface;
use Icecave\Chastity\Exception\LockAcquisitionException;
use Icecave\Chastity\Exception\LockAlreadyAcquiredException;
use Icecave\Chastity\Exception\LockNotAcquiredException;

class Lock implements LockInterface
{
    public function __construct(
        BlockingDriverInterface $driver,
        $resource,
        $token
    ) {
        $this->driver = $driver;
        $this->resource = $resource;
        $this->token = $token;
    }

    /**
     * Release this lock.
     */
    public function __destruct()
    {
        if ($this->isAcquired) {
            $this->driver->release(
                $this->resource,
                $this->token
            );
        }
    }

    /**
     * Get the resource to which this lock applies.
     *
     * @return string The resource to which this lock applies.
     */
    public function resource()
    {
        return $this->resource;
    }

    /**
     * Check if this lock has been acquired.
     *
     * @return boolean True if this lock is currently acquired by this process.
     */
    public function isAcquired()
    {
        if (!$this->isAcquired) {
            return false;
        }

        $this->isAcquired = $this->driver->isAcquired(
            $this->resource,
            $this->token
        );

        return $this->isAcquired;
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
        if ($this->tryAcquire($ttl, $timeout)) {
            return;
        }

        throw new LockAcquisitionException($this->resource);
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
        if ($this->isAcquired()) {
            throw new LockAlreadyAcquiredException($this->resource);
        }

        $this->isAcquired = $this->driver->acquire(
            $this->resource,
            $this->token,
            $ttl,
            $timeout
        );

        return $this->isAcquired;
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
        if ($this->isAcquired) {
            $this->isAcquired = $this->driver->extend(
                $this->resource,
                $this->token,
                $ttl
            );
        }

        if (!$this->isAcquired) {
            throw new LockNotAcquiredException($this->resource);
        }
    }

    /**
     * Release this lock.
     *
     * @throws LockNotAcquiredException if the lock has not been acquired.
     */
    public function release()
    {
        if (
            !$this->isAcquired
            || !$this->driver->release($this->resource, $this->token)
        ) {
            throw new LockNotAcquiredException($this->resource);
        }

        $this->isAcquired = false;
    }

    private $driver;
    private $resource;
    private $token;
    private $isAcquired;
}
