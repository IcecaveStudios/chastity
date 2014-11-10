<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Driver\BlockingAdaptor;
use Icecave\Chastity\Driver\BlockingDriverInterface;
use Icecave\Chastity\Driver\DriverInterface;
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
        $lock = $this->create($resource);

        $lock->acquire($ttl, $timeout);

        return $lock;
    }

    private $driver;
    private $uuidGenerator;
}
