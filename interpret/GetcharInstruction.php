<?php
/**
 * Instruction: GETCHAR <var> <symb1> <symb2> 
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class GetcharInstruction
 * Get the character at the specified index from the string and store it in the specified variable
 */
class GetcharInstruction extends Instruction {
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
        if (($src_type_1 !== "string") || (!$this->valid_types($src_type_1, $src_value_1)) || (($src_type_2 !== "int") || (!$this->valid_types($src_type_2, $src_value_2))))
            throw new OperandTypeException();
        
        // Check if the index is in range
        if (intval($src_value_2) >= mb_strlen($src_value_1))
            throw new StringOperationException();

        // Get the character at the specified index
        $final_value = $src_value_1[intval($src_value_2)];

        // Set the <var> value and type
        $this->set_value($this->arguments[0]->get_value(), $final_value, $this->arguments[0]->get_frame(), $interpret);
        $this->set_type($this->arguments[0]->get_value(), "string", $this->arguments[0]->get_frame(), $interpret);
        
        // Move to the next instruction
        $interpret->instructions_it++;
    }
}