<?php
/**
 * Instruction: MOVE <var> <symb>
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class MoveInstruction
 * Move the value of the specified symbol to the specified variable
 */
class MoveInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ((isset($this->arguments[0])) && (isset($this->arguments[1]))) {
            if (($this->arg_count !== 2) || (!$this->check_symb($this->arguments[1]->get_type())))
                throw new InvalidSourceException();
        } else {
            throw new InvalidSourceException();
        }

        // Get the variable type and value
        $src_value = $this->get_value($this->arguments[1]->get_type(), $this->arguments[1]->get_value(), $this->arguments[1]->get_frame(), $interpret);
        $src_type = $this->get_type($this->arguments[1]->get_type(), $this->arguments[1]->get_value(), $this->arguments[1]->get_frame(), $interpret);

        // Undefined variable
        if ($src_value === null)
            throw new SemanticException();

        if (!$this->valid_types($src_type, $src_value))
            throw new InvalidSourceException();

        $this->set_value($this->arguments[0]->get_value(), $src_value, $this->arguments[0]->get_frame(), $interpret);
        $this->set_type($this->arguments[0]->get_value(), $src_type, $this->arguments[0]->get_frame(), $interpret);

        $interpret->instructions_it++;
    }
}