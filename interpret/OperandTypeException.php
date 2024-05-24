<?php
/**
 * Error: Operand type
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

/**
 * Exception for operand type errors
 */
class OperandTypeException extends IPPException
{
    public function __construct(string $message = "Operand type error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::OPERAND_TYPE_ERROR, $previous, false); // Call the parent class constructor
    }
}
