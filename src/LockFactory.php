<?php
namespace Icecave\Chastity;

use Icecave\Chastity\Driver\BlockingAdaptor;
use Icecave\Chastity\Driver\BlockingDriverInterface;
use Icecave\Chastity\Driver\DriverInterface;
use Icecave\Chastity\Exception\LockAcquisitionException;
use Icecave\Druid\UuidGeneratorInterface;
use Icecave\Druid\UuidVersion4Generator;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class LockFactory implements LockFactoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param DriverInterface             $driver        The lock driver.
     * @param UuidGeneratorInterface|null $uuidGenerator The UUID generator used to create unique lock tokens.
     */
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

        $this->setDefaultTtl(600);
    }

    /**
     * Get the default TTL to use when acquiring locks.
     *
     * @return integer|float How long the lock should persist, in seconds.
     */
    public function defaultTtl()
    {
        return $this->defaultTtl;
    }

    /**
     * Set the default TTL to use when acquiring locks.
     *
     * @param integer|float $ttl How long the lock should persist, in seconds.
     */
    public function setDefaultTtl($ttl)
    {
        $this->defaultTtl = $ttl;
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

        $lock = new Lock(
            $this->driver,
            $resource,
            $token
        );

        if ($this->logger) {
            $lock->setLogger($this->logger);
        }

        return $lock;
    }

    /**
     * Attempt to acquire a lock and throw an exception if acquisition is
     * unsuccessful.
     *
     * This is a convenience method analogous to calling acquire() on the lock
     * returned by a call to $this->create($resource).
     *
     * @param string             $resource The resource to lock.
     * @param integer|float|null $ttl      How long the lock should persist, in seconds, or null to use the default.
     * @param integer|float      $timeout  How long to wait for lock acquisition, in seconds.
     *
     * @return LockInterface            An acquired lock for the given resource.
     * @throws LockAcquisitionException if the lock can not be acquired.
     */
    public function acquire($resource, $ttl = null, $timeout = INF)
    {
        if (null === $ttl) {
            $ttl = $this->defaultTtl;
        }

        $lock = $this->create($resource);

        $lock->acquire($ttl, $timeout);

        return new ScopedLock($lock);
    }

    /**
     * Attempt to acquire a lock.
     *
     * This is a convenience method analogous to calling tryAcquire() on the
     * lock returned by a call to $this->create($resource).
     *
     * @param string             $resource The resource to lock.
     * @param integer|float|null $ttl      How long the lock should persist, in seconds, or null to use the default.
     * @param integer|float      $timeout  How long to wait for lock acquisition, in seconds.
     *
     * @return LockInterface|null An acquired lock for the given resource, or null if the lock could not be acquired.
     */
    public function tryAcquire($resource, $ttl = null, $timeout = INF)
    {
        if (null === $ttl) {
            $ttl = $this->defaultTtl;
        }

        $lock = $this->create($resource);

        if ($lock->tryAcquire($ttl, $timeout)) {
            return new ScopedLock($lock);
        }

        return null;
    }

    private $driver;
    private $uuidGenerator;
    private $defaultTtl;
}
