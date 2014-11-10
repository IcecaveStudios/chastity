<?php
namespace Icecave\Chastity\Exception;

use Exception;
use LogicException;

class LockAlreadyAcquiredException extends LogicException
{
    public function __construct($resource, Exception $previous = null)
    {
        parent::__construct(
            'Lock has already been acquired: ' . $resource . '.',
            0,
            $previous
        );
    }
}
