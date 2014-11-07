<?php
namespace Icecave\Chastity\Exception;

use RuntimeException;

class LockAcquisitionException extends RuntimeException
{
    public function __construct($name)
    {
        parent::__construct('Unable to acquire lock: ' . $name . '.');
    }
}
