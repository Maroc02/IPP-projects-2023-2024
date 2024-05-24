<?php
/**
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class Variable
 * Represents a variable argument of an instruction
 */
class Variable extends Argument {
    private string $frame;
    private string $value;

    public function __construct(string $arg_type, int $arg_order, string $frame_type, string $var_value) {
        parent::__construct($arg_type, $arg_order); // Call the parent class constructor

        // Validate the frame and value
        if (($frame_type !== "GF" && $frame_type !== "LF" && $frame_type !== "TF") || ($var_value === "") || (!preg_match('/^[-_$&%*!?a-zA-Z][-_$&%*!?a-zA-Z0-9]*$/', $var_value)))
            throw new InvalidSourceException();

        // Set the frame and value
        $this->frame = $frame_type;
        $this->value = $var_value;
    }

    // Get the frame of the argument
    public function get_frame(): string {
        return $this->frame;
    }

    // Get the value of the argument
    public function get_value(): string {
        return $this->value;
    }       
}