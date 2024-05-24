<?php
/**
 * Instruction: JUMP <label> 
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class JumpInstruction
 * Jump to the specified label
 */
class JumpInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ($this->arg_count !== 1)
            throw new InvalidSourceException();

        // Check if the label exists
        if (!array_key_exists($this->arguments[0]->get_label(), $interpret->get_labels()))
            throw new SemanticException();

        // Jump to the specified label
        $interpret->instructions_it = intval($interpret->get_labels()[$this->arguments[0]->get_label()]);
    }
}