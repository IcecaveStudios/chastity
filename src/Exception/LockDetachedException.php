<?php
namespace Icecave\Chastity\Exception;

use Exception;
use LogicException;

/**
 * A lock operation has been attempted on a detatched scoped lock.
 *
 * @see ScopedLockInterface::detach()
 */
class LockDetachedException extends LogicException
{
    /**
     * @param Exception|null $previous The previous exception.
     */
    public function __construct(Exception $previous = null)
    {
        parent::__construct(
            'Lock has been detached.',
            0,
            $previous
        );
    }
}
