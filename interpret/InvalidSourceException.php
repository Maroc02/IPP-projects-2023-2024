<?php
/**
 * Error: Invalid source
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

/**
 * Exception for invalid source structure errors
 */
class InvalidSourceException extends IPPException
{
    public function __construct(string $message = "Invalid source structure error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::INVALID_SOURCE_STRUCTURE, $previous, false); // Call the parent class constructor
    }
}
