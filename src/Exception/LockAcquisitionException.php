<?php
namespace Icecave\Chastity\Exception;

use Exception;
use RuntimeException;

class LockAcquisitionException extends RuntimeException
{
    public function __construct($name, Exception $previous = null)
    {
        parent::__construct(
            'Unable to acquire lock: ' . $name . '.',
            0,
            $previous
        );
    }
}
