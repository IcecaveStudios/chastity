<?php
namespace Icecave\Chastity\Driver\Redlock;

use Icecave\Chastity\Driver\DriverInterface;
use Icecave\Chastity\Driver\Exception\DriverUnavailableException;
use Icecave\Isolator\IsolatorTrait;
use SplObjectStorage;

/**
 * An implementation of the "Redlock" distributed locking algorithm.
 *
 * @link http://redis.io/topics/distlock
 *
 * Although the Redlock algorithm was originally designed for use with Redis,
 * this driver can be used with any well-behaved Chastity driver.
 */
class RedlockDriver implements DistributedDriverInterface
{
    use IsolatorTrait;

    /**
     * @param array<DriverInterface> $nodes      The underlying drivers used to implement the distributed lock.
     * @param integer|float          $retryDelay How long to wait before retrying a driver that is unavailable, in seconds.
     * @param integer|float          $ttlPadding Additional time to add to the TTL per driver.
     */
    public function __construct(
        array $drivers,
        $ttlPadding = 0.1
    ) {
        foreach ($drivers as $driver) {
            if (!$driver instanceof DriverInterface) {
                throw new InvalidArgumentException(
                    'Drivers array must contain only instances of ' . DriverInterface::class
                );
            }
        }

        $this->drivers    = $drivers;
        $this->ttlPadding = $ttlPadding;
        $this->quorum     = floor($this->nodes / 2) + 1;
    }

    /**
     * @return array<DriverInterface> The underlying lock drivers.
     */
    public function drivers()
    {
        return $this->drivers;
    }

    /**
     * Acquire a lock on the given resource.
     *
     * @param string        $resource The resource to lock.
     * @param string        $token    The unique token representing the acquisition request.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     * @param integer|float $timeout  How long to wait for the lock to be acquired, in seconds.
     *
     * @return boolean                    True if the lock is acquired; otherwise, false.
     * @throws DriverUnavailableException if the driver is not available at the current time.
     */
    public function acquire($resource, $token, $ttl, $timeout)
    {
        throw new \Exception('Not implemented.');
    }

    /**
     * Extend the TTL of a lock that has already been acquired.
     *
     * @param string        $resource The locked resource.
     * @param string        $token    The token originally passed to acquire().
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     *
     * @return boolean                    True if the lock is acquired and has been extended; otherwise, false.
     * @throws DriverUnavailableException if the driver is not available at the current time.
     */
    public function extend($resource, $token, $ttl)
    {
        $iso       = $this->isolator();
        $successes = [];
        $failures  = [];
        $expiry    = INF;
        $paddedTTL = $ttl + (count($this->drivers) * $this->ttlPadding);

        foreach ($this->drivers as $driver) {

            // Attempt to extend the lock ...
            try {
                $driverTTL = $driver->extend($resource, $token, $paddedTTL);
            } catch (DriverUnavailableException $e) {
                $driverTTL = 0;
            }

            // Reduce the TTL padding ...
            $paddedTTL -= $this->ttlPadding;

            // The lock was not extended ...
            if ($driverTTL <= 0) {
                $failures[] = $driver;

                // We can not reach quorum, bail early ...
                if (count($failures) >= $this->quorum) {
                    break;
                }

                // We may yet reach quorum, try the next driver ...
                continue;
            }

            // Work out when this lock actually expires ...
            $driverExpiry = $iso->microtime(true) + $driverTTL;

            // Store the time of the earliest expiring lock ...
            if ($driverExpiry < $expiry) {
                $expiry = $driverExpiry;
            }

            $successes[] = $driver;
        }

        // Move successful drivers to the start of the array so they are tried
        // first next time ...
        $this->drivers = array_merge(
            $successes,
            $failures
        );

        // Quorum was reached ...
        if (count($successes) >= $this->quorum) {

            // Calculate the shortest TTL as of the current time ...
            $shortestTTL = $iso->microtime(true) - $expiry;

            // If the shortest TTL at least the request TTL then lock
            // acquisition was successful ...
            if ($shortestTTL >= $ttl) {
                return $ttl;
            }
        }

        // Quorum was not reached, release *everything* ...
        $this->release($resource, $token);

        return 0;
    }

    /**
     * Release a lock.
     *
     * @param string $resource The locked resource.
     * @param string $token    The token originally passed to acquire().
     *
     * @return boolean                    True if the lock was previously acquired; otherwise, false.
     * @throws DriverUnavailableException if the driver is not available at the current time.
     */
    public function release($resource, $token)
    {
        $count = 0;

        foreach ($this->drivers as $driver) {
            try {
                if ($driver->release($resource, $token)) {
                    ++$count;
                }
            } catch (DriverUnavailableException $e) {
                // ignore ...
            }
        }

        return $count >= $this->quorum;
    }

    private $nodes;
    private $retryDelay;
    private $ttlPadding;
    private $quorum;
}
