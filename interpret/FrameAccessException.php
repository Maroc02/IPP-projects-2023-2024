<?php
/**
 * Error: Frame access
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

/**
 * Exception for frame access errors
 */
class FrameAccessException extends IPPException
{
    public function __construct(string $message = "Frame access error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::FRAME_ACCESS_ERROR, $previous, false); // Call the parent class constructor
    }
}
