<?php
/**
 * Instruction: CALL <label>
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class CallInstruction
 * Save the current instruction pointer to the call stack and jump to the specified label
 */
class CallInstruction extends Instruction {
    public function __construct(int $order, string  $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ((!isset($this->arguments[0])) || ($this->arg_count !== 1) || ($this->arguments[0]->get_type() !== "label"))
            throw new InvalidSourceException();

        // Check if the label exists
        if (!array_key_exists($this->arguments[0]->get_label(), $interpret->get_labels()))
            throw new SemanticException();

        // Save the current instruction pointer to the call stack
        array_push($interpret->call_stack, intval($interpret->instructions_it));

        // Jump to the specified label
        $interpret->instructions_it = intval($interpret->get_labels()[$this->arguments[0]->get_label()]);
    }
}