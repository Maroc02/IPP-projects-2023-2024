<?php
/**
 * Instruction: DEFVAR <var>
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class StrI2IntInstruction
 * Define a new undefined variable in the specified frame
 */
class DefVarInstruction extends Instruction {
    public function __construct(int $order, string $opcode) {
        parent::__construct($order, $opcode); // Call the parent class constructor
    }

    // Execute the instruction
    public function execute(Interpret $interpret): void {
        // Validate the instruction arguments
        if ((!isset($this->arguments[0])) || ($this->arg_count !== 1) || ($this->arguments[0]->get_type() !== "var"))
            throw new InvalidSourceException();
        
        // Check the corresponding frame type
        switch($this->arguments[0]->get_frame()){
            case "GF":
                if (!empty($interpret->global_frame)){
                    // Check for variable redefinition
                    foreach ($interpret->global_frame as $variableName => $variableData){
                        if ($this->arguments[0]->get_value() === $variableName) {
                            throw new SemanticException();
                        }
                    }
                }
                // Add the variable to the frame
                $interpret->global_frame += [$this->arguments[0]->get_value() => [
                    "value" => null,
                    "type" => null]
                ];
                break;
            case "LF":
                // Check if the local frame exists 
                if ($interpret->frame_stack->isNull())
                    throw new FrameAccessException();

                if (!$interpret->frame_stack->isEmpty()) {
                    // Check for variable redefinition
                    foreach ($interpret->frame_stack->top() as $variableName => $variableData){
                        if ($this->arguments[0]->get_value() === $variableName)
                            throw new SemanticException();
                    }
                }
                // Add the variable to the frame
                $top_item = $interpret->frame_stack->pop();
                $top_item += [$this->arguments[0]->get_value() => [
                    "value" => null,
                    "type" => null]
                ];
                // Add the variable to the frame
                $interpret->frame_stack->push($top_item);
                break;

            case "TF":
                // Check if the temporary frame exists
                if ($interpret->temporary_frame === null)
                    throw new FrameAccessException();

                if (!empty($interpret->temporary_frame)){
                    // Check for variable redefinition
                    foreach ($interpret->temporary_frame as $variableName => $variableData){
                        if ($this->arguments[0]->get_value() === $variableName)
                            throw new SemanticException();
                    }
                }
                // Add the variable to the frame
                $interpret->temporary_frame += [$this->arguments[0]->get_value() => [
                    "value" => null,
                    "type" => null]
                ];
                break;
            // Invalid frame type
            default:
                throw new InvalidSourceException();
        }

        // Move to the next instruction
        $interpret->instructions_it++;
    }
}