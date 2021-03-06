<?php
namespace Icecave\Chastity\Driver;

use Icecave\Chastity\Driver\Exception\DriverUnavailableException;
use Icecave\Interlude\Exception\TimeoutException;
use Icecave\Interlude\Invoker;
use Icecave\Interlude\InvokerInterface;

/**
 * Emulates timeout-based blocking behaviour for a lock driver that must poll
 * in order to acquire a lock.
 */
trait PollingDriverTrait
{
    /**
     * Acquire a lock on the given resource.
     *
     * @param string        $resource The resource to lock.
     * @param string        $token    The unique token representing the acquisition request.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     *
     * @return integer|float              The actual remaining TTL of the lock, (ie, 0 if the lock could not be acquired).
     * @throws DriverUnavailableException if the driver is not available at the current time.
     */
    abstract public function poll($resource, $token, $ttl);

    /**
     * Acquire a lock on the given resource.
     *
     * @param string        $resource The resource to lock.
     * @param string        $token    The unique token representing the acquisition request.
     * @param integer|float $ttl      How long the lock should persist, in seconds.
     * @param integer|float $timeout  How long to wait for the lock to be acquired, in seconds.
     *
     * @return integer|float              The actual remaining TTL of the lock, (ie, 0 if the lock could not be acquired).
     * @throws DriverUnavailableException if the driver is not available at the current time.
     */
    public function acquire($resource, $token, $ttl, $timeout)
    {
        try {
            $acquired = $this
                ->invoker()
                ->invoke(
                    function () use ($resource, $token, $ttl) {
                        return $this->poll(
                            $resource,
                            $token,
                            $ttl
                        );
                    },
                    $timeout,
                    INF,
                    $this->pollPeriod
                );

            if ($acquired) {
                return $ttl;
            }
        } catch (TimeoutException $e) {
            // fall-through ...
        }

        return 0;
    }

    /**
     * Get the poll period.
     *
     * The poll period is the delay introduced between acquisition polls.
     *
     * @return integer|float The poll period, in seconds.
     */
    public function pollPeriod()
    {
        return $this->pollPeriod;
    }

    /**
     * Set the poll period.
     *
     * The poll period is the delay introduced between acquisition polls.
     *
     * @param integer|float $pollPeriod The poll period, in seconds.
     */
    public function setPollPeriod($pollPeriod)
    {
        $this->pollPeriod = $pollPeriod;
    }

    /**
     * Get the invoker used to emulate blocking behavior.
     *
     * @return InvokerInterface
     */
    public function invoker()
    {
        if (null === $this->invoker) {
            $this->invoker = new Invoker;
        }

        return $this->invoker;
    }

    /**
     * Set the invoker used to emulate blocking behavior.
     *
     * @param InvokerInterface $invoekr
     */
    public function setInvoker(InvokerInterface $invoker)
    {
        $this->invoker = $invoker;
    }

    private $invoker;
    private $pollPeriod = 0.1;
}
