<?php
/**
 * Instruction: NOT <var> <symb1>
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class NotInstruction
 * Perform logical NOT operation on boolean value and store the result in the specified variable
 */
class NotInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ((isset($this->arguments[0])) && (isset($this->arguments[1]))) {
            if (($this->arg_count !== 2) || ($this->arguments[0]->get_type() !== "var") || (!$this->check_symb($this->arguments[1]->get_type())))
                throw new InvalidSourceException();
        } else {
            throw new InvalidSourceException();
        }

        // Get the <symb1> value and type
        $src_value = $this->get_value($this->arguments[1]->get_type(), $this->arguments[1]->get_value(), $this->arguments[1]->get_frame(), $interpret);
        $src_type = $this->get_type($this->arguments[1]->get_type(), $this->arguments[1]->get_value(), $this->arguments[1]->get_frame(), $interpret);

        // Validate the types
        if (($src_type !== "bool") || (!$this->valid_types($src_type, $src_value)))
            throw new OperandTypeException();

        // Set the <var> value and type
        $this->set_value($this->arguments[0]->get_value(), ($src_value === "true") ? "false" : "true", $this->arguments[0]->get_frame(), $interpret);
        $this->set_type($this->arguments[0]->get_value(), "bool", $this->arguments[0]->get_frame(), $interpret);
        
        // Move to the next instruction
        $interpret->instructions_it++;
    }
}