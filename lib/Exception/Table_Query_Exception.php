<?php

namespace Yamoah\Exception;

/**
 * Inherits Yamoah standard Exception class
 */
class Table_Query_Exception extends Yamoah_Exception
{
    const CREATE_ERROR = 1;
    const USE_ERROR = 2;
    const ALTER_ERROR = 3;
    const DELETE_ERROR = 4;

    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}

?>