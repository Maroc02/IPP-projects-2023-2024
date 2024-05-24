<?php
/**
 * Instruction: BREAK
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class BreakInstruction
 * Print the program information to stderr
 */
class BreakInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ($this->arg_count !== 0)
            throw new InvalidSourceException();

        // Print the program information to stderr
        $interpret->get_stderr()->writeString("PROGRAM INFORMATION\n");
        $interpret->get_stderr()->writeString("Currently on instruction: BREAK\n");
        $interpret->get_stderr()->writeString("Instruction order: " . ($interpret->instructions_it + 1) . "\n");
        $interpret->get_stderr()->writeString("Number of instructions in total: " . count($interpret->instructions) . "\n");
        
        // Move to the next instruction
        $interpret->instructions_it++;
    }
}