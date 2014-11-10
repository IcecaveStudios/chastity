<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Driver\BlockingAdaptor;
use Icecave\Chastity\Driver\BlockingDriverInterface;
use Icecave\Chastity\Driver\DriverInterface;
use Icecave\Chastity\Exception\LockAcquisitionException;
use Icecave\Druid\UuidGeneratorInterface;
use Icecave\Druid\UuidVersion4Generator;

class LockFactory implements LockFactoryInterface
{
    public function __construct(
        DriverInterface $driver,
        UuidGeneratorInterface $uuidGenerator = null
    ) {
        if (!$driver instanceof BlockingDriverInterface) {
            $driver = new BlockingAdaptor($driver);
        }

        if (null === $uuidGenerator) {
            $uuidGenerator = new UuidVersion4Generator;
        }

        $this->driver = $driver;
        $this->uuidGenerator = $uuidGenerator;
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
        $token = $this
            ->uuidGenerator
            ->generate()
            ->string();

        return new Lock(
            $this->driver,
            $resource,
            $token
        );
    }

    /**
     * Attempt to acquire a lock and throw an exception if acquisition is
     * unsuccessful.
     *
     * This is a convenience method analogous to calling acquire() on the lock
     * returned by a call to $this->create($resource).
     *
     * @param string        $resource The resource to lock.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     * @param integer|float $timeout  How long to wait for lock acquisition, in seconds.
     *
     * @return LockInterface            An acquired lock for the given resource.
     * @throws LockAcquisitionException if the lock can not be acquired.
     */
    public function acquire($resource, $ttl, $timeout = INF)
    {
        $lock = $this->create($resource);

        $lock->acquire($ttl, $timeout);

        return $lock;
    }

    /**
     * Attempt to acquire a lock.
     *
     * This is a convenience method analogous to calling tryAcquire() on the
     * lock returned by a call to $this->create($resource).
     *
     * @param string        $resource The resource to lock.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     * @param integer|float $timeout  How long to wait for lock acquisition, in seconds.
     *
     * @return LockInterface|null An acquired lock for the given resource, or null if the lock could not be acquired.
     */
    public function tryAcquire($resource, $ttl, $timeout = INF)
    {
        $lock = $this->create($resource);

        if ($lock->tryAcquire($ttl, $timeout)) {
            return $lock;
        }

        return null;
    }

    private $driver;
    private $uuidGenerator;
}