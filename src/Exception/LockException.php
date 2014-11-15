<?php
namespace Icecave\Chastity\Exception;

use Exception;
use RuntimeException;

/**
 * Indicates that an attempt to acquire a lock has failed.
 */
class LockException extends RuntimeException
{
    /**
     * @param string         $resource The resource being locked.
     * @param Exception|null $previous The previous exception.
     */
    public function __construct($resource, Exception $previous = null)
    {
        parent::__construct(
            'Unable to acquire lock: ' . $resource . '.',
            0,
            $previous
        );
    }
}
