<?php
namespace Icecave\Chastity\Driver\Exception;

use Exception;
use RuntimeException;

class DriverUnavailableException extends RuntimeException
{
    public function __construct(Exception $previous = null)
    {
        parent::__construct(
            'The lock driver is unavailable.',
            0,
            $previous
        );
    }
}
