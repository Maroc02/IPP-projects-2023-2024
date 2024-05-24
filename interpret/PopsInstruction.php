<?php
/**
 * Instruction: POPS <var>
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class PopsInstruction
 * Pop the specified symbol from the data frame and save it to the variable
 */
class PopsInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ((!isset($this->arguments[0])) || ($this->arg_count !== 1) || ($this->arguments[0]->get_type() !== "var"))
            throw new InvalidSourceException();

        // Pop the value from the data frame
        $src_value = array_pop($interpret->data_frame);

        // Check if the variable is undefined
        if ($src_value == null) 
            throw new ValueException();

        // Set the <var> value and type
        $this->set_value($this->arguments[0]->get_value(), $src_value["value"], $this->arguments[0]->get_frame(), $interpret);
        $this->set_type($this->arguments[0]->get_value(), $src_value["type"], $this->arguments[0]->get_frame(), $interpret);

        // Move to the next instruction
        $interpret->instructions_it++;
    }
}