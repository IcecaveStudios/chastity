<?php
namespace Icecave\Chastity;

class LockFactory implements LockFactoryInterface
{
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }
    /**
     * Create a lock object for the given resource.
     *
     * @param string $resource The resource.
     *
     * @return LockInterface An unacquired lock for the given resource.
     */
    public function create($resource)
    {
        return new Lock(
            $this->driver,
            $resource
        );
    }

    /**
     * Acquire a lock for the given resource.
     *
     * This is a convenience method equivalent to acquiring the lock returned by
     * a call to $this->create($resource).
     *
     * @param string        $resource The resource to lock.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     * @param integer|float $timeout  How long to wait for lock acquisition, in seconds.
     *
     * @return LockInterface An acquired lock for the given resource.
     */
    public function acquire($resource, $ttl, $timeout = INF)
    {
        $lock = $this->create($name);
        $lock->acquire($ttl, $timeout);

        return $lock;
    }

    private $driver;
}
