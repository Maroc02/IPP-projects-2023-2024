<?php
/**
 * Instruction: POPFRAME
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class PopFrameInstruction
 * Pop the top frame from the frame stack
 */
class PopFrameInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ($this->arg_count !== 0)
            throw new InvalidSourceException();

        // Get the top frame from the stack
        $interpret->temporary_frame = $interpret->frame_stack->pop();
        
        // Move to the next instruction
        $interpret->instructions_it++;
    }
}