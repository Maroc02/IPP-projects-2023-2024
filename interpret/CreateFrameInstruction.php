<?php
/**
 * Instruction: CREATEFRAME
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class CreateFrameInstruction
 * Create a new temporary frame
 */
class CreateFrameInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ($this->arg_count !== 0)
            throw new InvalidSourceException();

        // Create new temporary frame
        $interpret->temporary_frame = [];

        // Move to the next instruction
        $interpret->instructions_it++;
    }
}