<?php
/**
 * Instruction: RETURN
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class ReturnInstruction
 * Pop the instruction pointer from the call stack and jump to the next instruction
 */
class ReturnInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ($this->arg_count !== 0)
            throw new InvalidSourceException();

        // Check if the call stack is empty
        if (empty($interpret->call_stack))
            throw new ValueException();

        // Pop the instruction pointer from the call stack
        $interpret->instructions_it = array_pop($interpret->call_stack) + 1;

        // Check if the instruction points to the last instruction
        if ($interpret->instructions_it == count($interpret->instructions))
            exit(0);
    }
}