<?php
namespace Icecave\Chastity\Driver;

use Icecave\Interlude\Invoker;
use Icecave\Interlude\InvokerInterface;

/**
 * Emulates blocking lock acquisition for non-blocking drivers.
 */
class BlockingAdaptor implements BlockingDriverInterface
{
    public function __construct(
        DriverInterface $driver,
        InvokerInterface $invoker = null,
        $delay = 0.1
    ) {
        if (null === $invoker) {
            $invoker = new Invoker();
        }

        $this->driver = $driver;
        $this->invoker = $invoker;
        $this->delay = $delay;
    }

    /**
     * Acquire a lock on the given resource.
     *
     * @param string        $resource The resource to lock.
     * @param string        $token    The unique token representing the acquisition request.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     * @param integer|float $timeout  How long to wait for the lock to be acquired, in seconds.
     *
     * @return boolean True if the lock is acquired; otherwise, false.
     */
    public function acquire($resource, $token, $ttl, $timeout = 0)
    {
        try {
            return $this->invoker->invoke(
                function () use ($resource, $token, $ttl) {
                    return $this->driver->acquire(
                        $resource,
                        $token,
                        $ttl
                    );
                },
                $timeout,
                INF,
                $this->delay
            );
        } catch (TimeoutException $e) {
            return false;
        }
    }

    /**
     * Check if the given token still represents an acquired lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token originally passed to acquire().
     *
     * @return boolean True if the lock is acquired; otherwise, false.
     */
    public function isAcquired($resource, $token)
    {
        return $this->driver->isAcquired($resource, $token);
    }

    /**
     * Extend the TTL of a lock that has already been acquired.
     *
     * @param string        $resource The locked resource.
     * @param string        $token    The token originally passed to acquire().
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     *
     * @return boolean True if the lock is acquired and has been extended; otherwise, false.
     */
    public function extend($resource, $token, $ttl)
    {
        return $this->driver->extend($resource, $token);
    }

    /**
     * Release a lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token originally passed to acquire().
     *
     * @return boolean True if the lock was previously acquired; otherwise, false.
     */
    public function release($resource, $token)
    {
        return $this->driver->release($resource, $token);
    }

    private $driver;
    private $invoker;
    private $delay;
}
