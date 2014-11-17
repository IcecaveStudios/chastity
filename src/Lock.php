<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Driver\DriverInterface;
use Icecave\Chastity\Exception\LockException;
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
     * @param DriverInterface $driver   The lock driver.
     * @param string          $resource The resource to which this lock applies.
     * @param string          $token    A unique token representing this lock instance.
     */
    public function __construct(
        DriverInterface $driver,
        $resource,
        $token
    ) {
        $this->driver   = $driver;
        $this->resource = $resource;
        $this->token    = $token;
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
     * Attempt to acquire this lock and throw an exception if acquisition is
     * unsuccessful.
     *
     * @param integer|float $ttl     How long the lock should persist, in seconds.
     * @param integer|float $timeout How long to wait for lock acquisition, in seconds.
     *
     * @throws LockException if the lock can not be acquired.
     */
    public function acquire($ttl, $timeout = INF)
    {
        if ($this->tryAcquire($ttl, $timeout)) {
            return;
        }

        throw new LockException($this->resource);
    }

    /**
     * Attempt to acquire this lock.
     *
     * @param integer|float $ttl     How long the lock should persist, in seconds.
     * @param integer|float $timeout How long to wait for lock acquisition, in seconds.
     *
     * @return boolean True if the lock is acquired; otherwise, false.
     */
    public function tryAcquire($ttl, $timeout = INF)
    {
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

        $isAcquired = $this->driver->acquire(
            $this->resource,
            $this->token,
            $ttl,
            $timeout
        );

        if ($this->logger) {
            if ($isAcquired) {
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

        return $isAcquired;
    }

    /**
     * Extend the TTL of this lock.
     *
     * @param integer|float $ttl How long the lock should persist, in seconds.
     *
     * @throws LockException if the lock has not been acquired.
     */
    public function extend($ttl)
    {
        $isAcquired = $this->driver->extend(
            $this->resource,
            $this->token,
            $ttl
        );

        if (!$isAcquired) {
            throw new LockException($this->resource);
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
     */
    public function release()
    {
        if (!$this->driver->release($this->resource, $this->token)) {
            return;
        } elseif ($this->logger) {
            $this->logger->debug(
                'Resource "{resource}" released by {token}',
                [
                    'resource' => $this->resource,
                    'token'    => $this->token,
                ]
            );
        }
    }

    private $driver;
    private $resource;
    private $token;
}
