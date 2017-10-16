<?php

namespace App\Security\Exception;

use Exception;
use RuntimeException;

class UnauthorizedException extends RuntimeException
{
    public function __construct($message = 'Unauthorized.', Exception $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}
