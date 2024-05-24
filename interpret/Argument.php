<?php
/**
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class Argument
 * Base class for an argument of an instruction
 */
class Argument {
    private string $argument_type;
    private int $argument_order;

    public function __construct(string $arg_type, int $arg_order){
        $this->argument_type = $arg_type;
        $this->argument_order = $arg_order;
    }

    // Get the type of the argument
    public function get_type(): string{
        return $this->argument_type;
    }

    // Get the order of the argument
    public function get_order(): int{
        return $this->argument_order;
    }

    public function get_frame(): ?string {
        throw new InvalidSourceException();
    }

    public function get_value(): string {
        throw new InvalidSourceException();
    }

    public function get_label(): string {
        throw new InvalidSourceException();
    }
}