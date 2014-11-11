<?php
namespace Icecave\Chastity\Exception;

use Exception;
use LogicException;

/**
 * An operation that requires a lock to be acquired can not be performed because
 * the lock has NOT been acquired.
 */
class LockNotAcquiredException extends LogicException
{
    /**
     * @param string         $resource The resource that is not locked.
     * @param Exception|null $previous The previous exception.
     */
    public function __construct($resource, Exception $previous = null)
    {
        parent::__construct(
            'Lock has not been acquired: ' . $resource . '.',
            0,
            $previous
        );
    }
}
