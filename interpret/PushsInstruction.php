<?php
/**
 * Instruction: PUSHS <symb>
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class PushsInstruction
 * Push the specified symbol to the data frame
 */
class PushsInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ((!isset($this->arguments[0])) || ($this->arg_count !== 1) || (!$this->check_symb($this->arguments[0]->get_type())))
            throw new InvalidSourceException();

        // Get the <symb> value and type
        $src_value = $this->get_value($this->arguments[0]->get_type(), $this->arguments[0]->get_value(), $this->arguments[0]->get_frame(), $interpret);
        $src_type = $this->get_type($this->arguments[0]->get_type(), $this->arguments[0]->get_value(), $this->arguments[0]->get_frame(), $interpret);

        // Check if the variable is undefined
        if ($src_value === null)
            throw new SemanticException();

        // Push the value to the data frame
        array_push($interpret->data_frame, ["value" => $src_value, "type" => $src_type]);

        // Move the instruction pointer
        $interpret->instructions_it++;
    }
}