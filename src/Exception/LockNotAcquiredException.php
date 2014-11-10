<?php
namespace Icecave\Chastity\Exception;

use Exception;
use LogicException;

class LockNotAcquiredException extends LogicException
{
    public function __construct($resource, Exception $previous = null)
    {
        parent::__construct(
            'Lock has not been acquired: ' . $resource . '.',
            0,
            $previous
        );
    }
}
