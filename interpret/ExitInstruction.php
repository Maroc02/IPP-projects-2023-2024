<?php
/**
 * Instruction: EXIT <symb> 
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class ExitInstruction
 * Exit the program with the specified return code
 */
class ExitInstruction extends Instruction {
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
        if (($src_type !== "int") || (!$this->valid_types($src_type, $src_value)))
            throw new OperandTypeException();

        // Check if the return code is in range
        if ((intval($src_value) < 0) || (intval($src_value) > 9))
            throw new OperandValueException();

        // Exit the program with the specified return code
        exit(intval($src_value));
    }
}