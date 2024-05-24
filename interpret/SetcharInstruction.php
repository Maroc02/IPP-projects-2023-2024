<?php
/**
 * Instruction: SETCHAR <var> <symb1> <symb2> 
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class SetcharInstruction
 * Set the character at the specified index in the string to the specified character
 */
class SetcharInstruction extends Instruction {
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

        // Get the <symb1> value and type
        $src_value_2 = $this->get_value($this->arguments[2]->get_type(), $this->arguments[2]->get_value(), $this->arguments[2]->get_frame(), $interpret);
        $src_type_2 = $this->get_type($this->arguments[2]->get_type(), $this->arguments[2]->get_value(), $this->arguments[2]->get_frame(), $interpret);

        // Get the <var> value and type
        $dst_value = $this->get_value($this->arguments[0]->get_type(), $this->arguments[0]->get_value(), $this->arguments[0]->get_frame(), $interpret);
        $dst_type = $this->get_type($this->arguments[0]->get_type(), $this->arguments[0]->get_value(), $this->arguments[0]->get_frame(), $interpret);

        // Validate the types
        if (($src_type_1 !== "int") || (!$this->valid_types($src_type_1, $src_value_1)) || (($src_type_2 !== "string") || (!$this->valid_types($src_type_2, $src_value_2))) || (($dst_type !== "string") || (!$this->valid_types($dst_type, $dst_value))))
            throw new OperandTypeException();

        // Check if the index is in range
        if ((intval($src_value_1) >= mb_strlen($dst_value)) || (empty($src_value_2) || (empty($dst_value))))
            throw new StringOperationException();

        // Set the character at the specified index
        $final_char = $src_value_2[0];
        $dst_value[$src_value_1] = $final_char;

        // Set the <var> value and type
        $this->set_value($this->arguments[0]->get_value(), $dst_value, $this->arguments[0]->get_frame(), $interpret);
        $this->set_type($this->arguments[0]->get_value(), "string", $this->arguments[0]->get_frame(), $interpret);
        
        // Move to the next instruction
        $interpret->instructions_it++;
    }
}