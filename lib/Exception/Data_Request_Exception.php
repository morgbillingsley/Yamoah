<?php

namespace Yamoah\Exception;

/**
 * Class: Data_Request_Exception
 */
class Data_Request_Exception extends Yamoah_Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 0);
    }
}

?>