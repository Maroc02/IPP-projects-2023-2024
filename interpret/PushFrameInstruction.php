<?php
/**
 * Instruction: PUSHFRAME
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class PushFrameInstruction
 * Move the temporary frame to the frame stack
 */
class PushFrameInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ($this->arg_count !== 0)
            throw new InvalidSourceException();

        // Check if the temporary frame is defined
        if ($interpret->temporary_frame === null)
            throw new FrameAccessException();

        // Push temporary frame to the stack
        $interpret->frame_stack->push($interpret->temporary_frame);
        
        // Clear temporary frame
        $interpret->temporary_frame = null;

        // Move to the next instruction
        $interpret->instructions_it++;
    }
}