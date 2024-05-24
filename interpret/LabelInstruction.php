<?php
/**
 * Instruction: LABEL <label> 
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class LabelInstruction
 * Create the specified label in the program
 */
class LabelInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ($this->arg_count !== 1)
            throw new InvalidSourceException();

        // Move to the next instruction
        $interpret->instructions_it++;
    }
}