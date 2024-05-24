<?php
/**
 * Instruction: DPRINT <symb> 
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class DPrintInstruction
 * Print the specified symbol to the stderr
 */
class DPrintInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if (($this->arg_count !== 1) || (!isset($this->arguments[0])))
            throw new InvalidSourceException();

        $value = null;
        
        // Check the <symb> type
        if ($this->arguments[0]->get_type() === "int") {
            // Get the <symb> value
            $value = $this->arguments[0]->get_value();

            // Validate the int value
            if (filter_var($value, FILTER_VALIDATE_INT) === false)
                    throw new InvalidSourceException();

            // Write the int value to the stderr
            $interpret->get_stderr()->writeInt(intval($value));
        } else if ($this->arguments[0]->get_type() === "string") {
            // Get the <symb> value
            $value = $this->arguments[0]->get_value();

            // Convert string
            $final_string = htmlspecialchars_decode($value, ENT_XML1);
            $regex = '/\\\\(0[0-9]{2}|0[0-2][0-9]|035|092)/';
            if (preg_match_all($regex, $final_string, $matches)) {
                foreach ($matches[1] as $match) {
                    $char = chr(intval($match));
                    $final_string = str_replace('\\' . $match, $char, $final_string);
                }
            }

            // Write the string value to the stderr
            $interpret->get_stderr()->writeString($final_string);
        } else if ($this->arguments[0]->get_type() === "bool") {
            // Get the <symb> value
            $value = $this->arguments[0]->get_value();
            // Validate the bool value
            if ($value !== "true" && $value !== "false")
                throw new InvalidSourceException();

            // Write the bool value to the stderr
            $interpret->get_stderr()->writeBool($value === "true" ? true : false);
        }
        else if ($this->arguments[0]->get_type() === "var") {
            // Get the <symb> value
            $value = $this->get_value($this->arguments[0]->get_type(), $this->arguments[0]->get_value(), $this->arguments[0]->get_frame(), $interpret);

            // Validate the types
            if (filter_var($value, FILTER_VALIDATE_INT) == true) {

                // Write the int value to the stderr
                $interpret->get_stderr()->writeInt(intval($value));
            } else if (($value === "true") || ($value === "false")) {

                // Write the string value to the stderr
                $interpret->get_stderr()->writeBool($value === "true" ? true : false);
            } else {
                // Convert string
                $final_string = htmlspecialchars_decode($value, ENT_XML1);
                // Replace escape sequences
                $regex = '/\\\\(0[0-9]{2}|0[0-2][0-9]|035|092)/';
                if (preg_match_all($regex, $final_string, $matches)) {
                    foreach ($matches[1] as $match) {
                        $char = chr(intval($match));
                        $final_string = str_replace('\\' . $match, $char, $final_string);
                    }
                }
                
                // Write the string value to the stderr
                $interpret->get_stderr()->writeString($final_string);
            }
        } else {
            throw new OperandTypeException();
        }

        // Move to the next instruction
        $interpret->instructions_it++;
    }
}