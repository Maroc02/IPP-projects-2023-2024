<?php
/**
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class Label
 * Represents a label argument of an instruction
 */
class Label extends Argument {
    private string $label;

    public function __construct(string $arg_type, int $arg_order, string $label_name) {
        parent::__construct($arg_type, $arg_order); // Call the parent class constructor

        // Validate the label
        if (!preg_match('/^[-_$&%*!?a-zA-Z][-_$&%*!?a-zA-Z0-9]*$/', $label_name))
            throw new InvalidSourceException();

        // Set the label
        $this->label = $label_name;
    }

    // Get the label of the argument
    public function get_label(): string {
        return $this->label;
    }
}