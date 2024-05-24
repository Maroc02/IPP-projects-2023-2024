<?php
/**
 * Instruction: GT <var> <symb1> <symb2>
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class GTInstruction
 * Compare two values of the same type and store the bool result in the specified variable
 */
class GTInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ((isset($this->arguments[0])) && (isset($this->arguments[1])) && (isset($this->arguments[2]))) {
            if (($this->arg_count !== 3) || ($this->arguments[0]->get_type() !== "var") || (!$this->check_symb($this->arguments[1]->get_type())) || (!$this->check_symb($this->arguments[2]->get_type())))
                throw new InvalidSourceException();
        } else {
            throw new InvalidSourceException();
        }

        // Get the <symb1> value and type
        $src_value_1 = $this->get_value($this->arguments[1]->get_type(), $this->arguments[1]->get_value(), $this->arguments[1]->get_frame(), $interpret);
        $src_type_1 = $this->get_type($this->arguments[1]->get_type(), $this->arguments[1]->get_value(), $this->arguments[1]->get_frame(), $interpret);

        // Get the <symb2> value and type
        $src_value_2 = $this->get_value($this->arguments[2]->get_type(), $this->arguments[2]->get_value(), $this->arguments[2]->get_frame(), $interpret);
        $src_type_2 = $this->get_type($this->arguments[2]->get_type(), $this->arguments[2]->get_value(), $this->arguments[2]->get_frame(), $interpret);

        // Validate the types
        if (($src_type_1 === "bool") && ($src_type_2 === "bool") && ($this->valid_types($src_type_1, $src_value_1)) && ($this->valid_types($src_type_2, $src_value_2))) {
            // Set the <var> value
            $this->set_value($this->arguments[0]->get_value(), ($src_value_1 === "true" && $src_value_2 === "false") ? "true" : "false", $this->arguments[0]->get_frame(), $interpret);
        } else if (($src_type_1 === "int") && ($src_type_2 === "int") && ($this->valid_types($src_type_1, $src_value_1)) && ($this->valid_types($src_type_2, $src_value_2))) {
            // Set the <var> value
            $this->set_value($this->arguments[0]->get_value(), (intval($src_value_1) > intval($src_value_2)) ? "true" : "false", $this->arguments[0]->get_frame(), $interpret);
        } else if (($src_type_1 === "string") && ($src_type_2 === "string") && ($this->valid_types($src_type_1, $src_value_1)) && ($this->valid_types($src_type_2, $src_value_2))) {
            // Set the <var> value
            $this->set_value($this->arguments[0]->get_value(), $src_value_1 > $src_value_2 ? "true" : "false", $this->arguments[0]->get_frame(), $interpret);
        } else {
            throw new OperandTypeException();
        }

        // Set the <var> type
        $this->set_type($this->arguments[0]->get_value(), "bool", $this->arguments[0]->get_frame(), $interpret);
        
        // Move to the next instruction
        $interpret->instructions_it++;
    }
}