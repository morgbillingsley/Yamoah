<?php

namespace Yamoah\Exception;

/**
 * Inherits Yamoah standard Exception class
 */
class Data_Query_Exception extends Yamoah_Exception
{
    const CREATE_ERROR = 1;
    const SELECT_ERROR = 2;
    const UPDATE_ERROR = 3;
    const DELETE_ERROR = 4;

    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}

?>