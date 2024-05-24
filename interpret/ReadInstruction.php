<?php
/**
 * Instruction: READ <var> <type> 
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class ReadInstruction
 * Read a value from the standard input and store it in the specified variable
 */
class ReadInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ((!isset($this->arguments[0])) || (!isset($this->arguments[1])) || ($this->arg_count !== 2) || ($this->arguments[0]->get_type() !== "var"))
            throw new InvalidSourceException();
        
        $read_value = "";
        $read_type = "";

        // Check the read type
        switch($this->arguments[1]->get_value()){
            case "int":
                // Read the int value from the standard input
                $read_value = $interpret->get_stdin()->readInt();
                if ($read_value !== null) {
                    $read_value = intval($read_value);
                    $read_type = "int"; 
                }

                break;
            case "string":
                // Read the string value from the standard input
                $read_value = $interpret->get_stdin()->readString(); 
                if ($read_value !== null) {
                    $read_type = "string";
                }

                break;
            case "bool":
                // Read the bool value from the standard input
                $read_value = $interpret->get_stdin()->readBool();
                if ($read_value !== null) {
                    $read_value = ($read_value == true) ? "true" : "false";
                    $read_type = "bool";
                }
                break;
            default:
                throw new InvalidSourceException();
            }
                
        // Check if the value is empty
        if (empty($read_value)) {
            $read_value = "nil";
            $read_type = "nil";
        }

        // Validate the types
        if (!$this->valid_types($read_type, $read_value))
            throw new OperandTypeException();

        // Set the <var> value and type
        $this->set_value($this->arguments[0]->get_value(), $read_value, $this->arguments[0]->get_frame(), $interpret);
        $this->set_type($this->arguments[0]->get_value(), $read_type, $this->arguments[0]->get_frame(), $interpret);

        // Move to the next instruction
        $interpret->instructions_it++;
    }
}