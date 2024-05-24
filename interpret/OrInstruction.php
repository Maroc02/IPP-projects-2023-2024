<?php
/**
 * Instruction: OR <var> <symb1> <symb2> 
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class OrInstruction
 * Perform logical OR operation on two boolean values and store the result in the specified variable
 */
class OrInstruction extends Instruction {
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
        if (($src_type_1 !== "bool") || ($src_type_2 !== "bool") || (!$this->valid_types($src_type_1, $src_value_1)) || (!$this->valid_types($src_type_2, $src_value_2)))
            throw new OperandTypeException();

        // Set the <var> value and type
        $this->set_value($this->arguments[0]->get_value(), ($src_value_1 === "false" && $src_value_2 === "false") ? "false" : "true", $this->arguments[0]->get_frame(), $interpret);
        $this->set_type($this->arguments[0]->get_value(), "bool", $this->arguments[0]->get_frame(), $interpret);
        
        // Move to the next instruction
        $interpret->instructions_it++;
    }
}