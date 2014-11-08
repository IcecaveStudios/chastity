<?php
namespace Icecave\Chastity\Exception;

use Exception;
use LogicException;

class LockNotAcquiredException extends LogicException
{
    public function __construct($name, Exception $previous = null)
    {
        parent::__construct(
            'Lock has not been acquired: ' . $name . '.',
            0,
            $previous
        );
    }
}
