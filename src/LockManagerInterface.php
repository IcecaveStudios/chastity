<?php
namespace Icecave\Chastity;

interface LockManagerInterface
{
    /**
     * Get a lock by name.
     *
     * @param string $name The name of lock.
     *
     * @return LockInterface
     */
    public function get($name);
}
