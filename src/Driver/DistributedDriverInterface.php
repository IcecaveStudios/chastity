<?php
namespace Icecave\Chastity\Driver;

/**
 * A lock driver that aggregates several other lock drivers to form a
 * distributed lock.
 */
interface DistributedDriverInterface extends DriverInterface
{
    /**
     * @return array<DriverInterface> The underlying lock drivers.
     */
    public function drivers();
}
