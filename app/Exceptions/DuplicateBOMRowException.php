<?php

namespace App\Exceptions;

use Exception;

class DuplicateBOMRowException extends Exception
{
    /**
     * Create a new Duplicate BOM Row Exception instance.
     *
     * @param string $message
     */
    public function __construct($message = "Duplicate BOM row detected.")
    {
        parent::__construct($message);
    }
}
