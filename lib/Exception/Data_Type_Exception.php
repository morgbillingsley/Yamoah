<?php

namespace Yamoah\Exception;

/**
 * Inherits Yamoah standard Exception class
 */
class Data_Type_Exception extends Yamoah_Exception
{
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }
}

?>