<?php
/**
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class Constant
 * Represents a constant argument of an instruction
 */
class Constant extends Argument {
    private ?string $frame;
    private string $value;

    public function __construct(string $arg_type, int $arg_order, ?string $frame_type, string $var_value){
        parent::__construct($arg_type, $arg_order); // Call the parent class constructor

        // Validate the argument type
        if ($arg_type === "int" && filter_var($var_value, FILTER_VALIDATE_INT) === false)
            throw new InvalidSourceException();
        else if ($arg_type === "bool" && !($var_value === "true" || $var_value === "false"))
            throw new InvalidSourceException();
        else if ($arg_type === "string" && !preg_match('/^([^#\\\\]|\\\\[0-9]{3})*$/', $var_value))
            throw new InvalidSourceException();
        else if ($arg_type === "nil" && !($var_value === "nil"))
            throw new InvalidSourceException();

        // Convert string
        if ($arg_type === "string") {
            if (preg_match_all('/\\\\(0[0-9]{2}|0[0-2][0-9]|035|092)/', $var_value, $matches)) {
                foreach ($matches[1] as $match) {
                    $char = chr(intval($match));
                    $var_value = str_replace('\\' . $match, $char, $var_value);
                }
            }
        }

        // Set the frame and value
        $this->frame = $frame_type;
        $this->value = $var_value;
    }

    // Get the frame of the argument
    public function get_frame(): ?string {
        return $this->frame;
    }

    // Get the value of the argument
    public function get_value(): string {
        return $this->value;
    }   
}