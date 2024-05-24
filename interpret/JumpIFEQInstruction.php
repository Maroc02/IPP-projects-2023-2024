<?php
/**
 * Instruction: JUMPIFEQ <label> <symb1> <symb2> 
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class JumpIFEQInstruction
 * Jump to the specified label if the compared values are equal
 */
class JumpIFEQInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ((isset($this->arguments[0])) && (isset($this->arguments[1])) && (isset($this->arguments[2]))) {
            if (($this->arg_count !== 3) || ($this->arguments[0]->get_type() !== "label") || (!$this->check_symb($this->arguments[1]->get_type())) || (!$this->check_symb($this->arguments[2]->get_type())))
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
        if (($src_type_1 !== $src_type_2) && ($src_type_1 !== "nil" && $src_type_2 !== "nil"))
            throw new OperandTypeException();
        
        // Check if the values are equal
        if ($src_value_1 == $src_value_2){
            // Check if the label exists
            if (!array_key_exists($this->arguments[0]->get_label(), $interpret->get_labels()))
                    throw new SemanticException();

            // Jump to the specified label
            $interpret->instructions_it = intval($interpret->get_labels()[$this->arguments[0]->get_label()]);
        } else {
            // Move to the next instruction
            $interpret->instructions_it++;
        }
    }
}