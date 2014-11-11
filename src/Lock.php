<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Driver\BlockingDriverInterface;
use Icecave\Chastity\Exception\LockAcquisitionException;
use Icecave\Chastity\Exception\LockAlreadyAcquiredException;
use Icecave\Chastity\Exception\LockNotAcquiredException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * @internal
 *
 * Default lock implementation.
 *
 * Note that locks should not be constructed directly, rather they should
 * created via using a {@see LockFactoryInterface}.
 */
class Lock implements LockInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param BlockingDriverInterface $driver   The lock driver.
     * @param string                  $resource The resource to which this lock applies.
     * @param string                  $token    A unique token representing this lock instance.
     */
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
     * Attempt to acquire this lock and throw an exception if acquisition is
     * unsuccessful.
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

        if ($this->logger) {
            $this->logger->debug(
                'Resource "{resource}" lock request from {token} with {ttl} second TTL and {timeout} second timeout',
                [
                    'resource' => $this->resource,
                    'token'    => $this->token,
                    'ttl'      => $ttl,
                    'timeout'  => $timeout,
                ]
            );
        }

        $this->isAcquired = $this->driver->acquire(
            $this->resource,
            $this->token,
            $ttl,
            $timeout
        );

        if ($this->logger) {
            if ($this->isAcquired) {
                $this->logger->debug(
                    'Resource "{resource}" successfully locked by {token}',
                    [
                        'resource' => $this->resource,
                        'token'    => $this->token,
                    ]
                );
            } else {
                $this->logger->debug(
                    'Resource "{resource}" could not be locked by {token}',
                    [
                        'resource' => $this->resource,
                        'token'    => $this->token,
                    ]
                );
            }
        }

        return $this->isAcquired;
    }

    /**
     * Extend the TTL of this lock.
     *
     * @param integer|float $ttl How long the lock should persist, in seconds.
     *
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
        } elseif ($this->logger) {
            $this->logger->debug(
                'Resource "{resource}" lock extended by {token} with {ttl} second TTL',
                [
                    'resource' => $this->resource,
                    'token'    => $this->token,
                    'ttl'      => $ttl,
                ]
            );
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
        } elseif ($this->logger) {
            $this->logger->debug(
                'Resource "{resource}" released by {token}',
                [
                    'resource' => $this->resource,
                    'token'    => $this->token,
                ]
            );
        }

        $this->isAcquired = false;
    }

    private $driver;
    private $resource;
    private $token;
    private $isAcquired;
}
