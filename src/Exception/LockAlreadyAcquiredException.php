<?php
namespace Icecave\Chastity\Exception;

use Exception;
use LogicException;

/**
 * An attempt has been made to acquire a lock that is already acquired.
 */
class LockAlreadyAcquiredException extends LogicException
{
    /**
     * @param string         $resource The resource that is locked.
     * @param Exception|null $previous The previous exception.
     */
    public function __construct($resource, Exception $previous = null)
    {
        parent::__construct(
            'Lock has already been acquired: ' . $resource . '.',
            0,
            $previous
        );
    }
}
