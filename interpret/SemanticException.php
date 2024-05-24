<?php
/**
 * Error: Semantic
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

/**
 * Exception for semantic errors
 */
class SemanticException extends IPPException
{
    public function __construct(string $message = "Semantic error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::SEMANTIC_ERROR, $previous, false); // Call the parent class constructor
    }
}
