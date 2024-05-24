<?php
/**
 * Error: Value
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

/**
 * Exception for value errors
 */
class ValueException extends IPPException
{
    public function __construct(string $message = "Value error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::VALUE_ERROR, $previous, false); // Call the parent class constructor
    }
}
