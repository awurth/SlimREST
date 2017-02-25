<?php

namespace App\Exception;

use Exception;

class AccessDeniedException extends Exception
{
    public function __construct($message = "Access denied", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
