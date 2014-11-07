<?php
namespace Icecave\Chastity\Redis;

use Icecave\Chastity\LockManagerInterface;

class RedisLockManager implements LockManagerInterface
{
    /**
     * Get a lock by name.
     *
     * @param string $name The name of lock.
     *
     * @return LockInterface
     */
    public function get($name)
    {
        return new RedisLock($name);
    }
}
