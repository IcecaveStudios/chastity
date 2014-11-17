<?php
namespace Icecave\Chastity;

/**
 * Automatically releases an underlying lock upon destruction.
 */
interface ScopedLockInterface extends LockInterface
{
    /**
     * Release the underlying lock.
     */
    public function __destruct();

    /**
     * Detach this scoped lock from its underlying lock.
     *
     * A detached scoped lock will no longer automatically release the lock on
     * destruction.
     *
     * @return LockInterface The underlying lock.
     */
    public function detach();
}
