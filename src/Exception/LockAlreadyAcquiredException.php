<?php
namespace Icecave\Chastity\Exception;

use Exception;
use LogicException;

class LockAlreadyAcquiredException extends LogicException
{
    public function __construct($name, Exception $previous = null)
    {
        parent::__construct(
            'Lock has already been acquired: ' . $name . '.',
            0,
            $previous
        );
    }
}
