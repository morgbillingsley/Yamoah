<?php

namespace Yamoah\Exception;

/**
 * Inherits php standard Exception class
 */
class Yamoah_Exception extends \Exception
{
    public function __construct(string $message, int $code = 0, \Exception $previos = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

?>