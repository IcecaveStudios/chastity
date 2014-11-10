<?php
namespace Icecave\Chastity\Exception;

use Exception;
use RuntimeException;

class LockAcquisitionException extends RuntimeException
{
    public function __construct($resource, Exception $previous = null)
    {
        parent::__construct(
            'Unable to acquire lock: ' . $resource . '.',
            0,
            $previous
        );
    }
}
