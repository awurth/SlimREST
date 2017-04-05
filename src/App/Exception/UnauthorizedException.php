<?php

namespace App\Exception;

use Exception;

class UnauthorizedException extends Exception
{
    public function __construct($message = 'Unauthorized', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
