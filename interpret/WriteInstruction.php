<?php
/**
 * Instruction: WRITE <symb> 
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class WriteInstruction
 * Write the specified symbol to the standard output
 */
class WriteInstruction extends Instruction {
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

        // Validate the types
        if (($src_type == "int") && ($this->valid_types($src_type, $src_value))) {
            // Write the int value to the standard output
            $interpret->get_stdout()->writeInt(intval($src_value));
        } else if (($src_type == "string") && ($this->valid_types($src_type, $src_value))) {
            // Write the string value to the standard output
            $interpret->get_stdout()->writeString($src_value);
        } else if (($src_type == "bool") && ($this->valid_types($src_type, $src_value))) {
            // Write the bool value to the standard output
            $interpret->get_stdout()->writeBool($src_value == "true" ? true : false);
        } else if (($src_type == "nil") && ($this->valid_types($src_type, $src_value))) {
            // Write the nil value to the standard output
            $interpret->get_stdout()->writeString('');
        } else {
            throw new OperandTypeException();
        }
        
        // Move to the next instruction
        $interpret->instructions_it++;
    }
}