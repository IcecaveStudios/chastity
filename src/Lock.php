<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Exception\LockAcquisitionException;
use Icecave\Chastity\Exception\LockAlreadyAcquiredException;
use Icecave\Chastity\Exception\LockNotAcquiredException;

class Lock implements LockInterface
{
    /**
     * @param DriverInterface $driver   The driver used to acquire/release locks.
     * @param string          $resource The resource.
     */
    public function __construct(DriverInterface $driver, $resource)
    {
        $this->driver = $driver;
        $this->resource = $resource;
    }

    /**
     * Release this lock.
     */
    public function __destruct()
    {
        if ($this->token) {
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
        if (!$this->token) {
            return false;
        }

        $isAcquired = $this->driver->isAcquired(
            $this->resource,
            $this->token
        );

        if ($isAcquired) {
            return true;
        }

        $this->token = null;

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

        $this->token = $this->driver->acquire(
            $this->resource,
            $ttl,
            $timeout
        );

        return (bool) $this->token;
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
        if (!$this->token) {
            throw new LockNotAcquiredException($this->resource);
        } elseif ($this->driver->extend($this->token, $ttl)) {
            return;
        }

        $this->token = null;

        throw new LockNotAcquiredException($this->resource);
    }

    /**
     * Release this lock.
     *
     * @throws LockNotAcquiredException if the lock has not been acquired.
     */
    public function release()
    {
        if (!$this->token) {
            throw new LockNotAcquiredException($this->resource);
        } elseif (!$this->driver->release($this->resource, $this->token)) {
            throw new LockNotAcquiredException($this->resource);
        }

        $this->token = null;
    }

    private $driver;
    private $resource;
    private $token;
}
