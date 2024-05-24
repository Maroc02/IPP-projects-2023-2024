<?php
/**
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class Type
 * Represents a type argument of an instruction
 */
class Type extends Argument {
    private string $value;

    public function __construct(string $arg_type, int $arg_order, string $type_value) {
        parent::__construct($arg_type, $arg_order); // Call the parent class constructor

        // Check the value type
        if (($type_value !== "int") && ($type_value !== "string") && ($type_value !== "bool"))
            throw new InvalidSourceException();
         
        // Set the value
        $this->value = $type_value;
    }

    // Get the value of the argument
    public function get_value(): string {
        return $this->value;
    }  
}
 