<?php
/**
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class Instruction
 * Represents an instruction
 */
abstract class Instruction {
    protected int $order;
    protected string $opcode;
    protected int $arg_order;
    protected int $arg_count;
    /**
     * @var array<Argument> Array of Instruction objects.
     */
    protected $arguments;

    public function __construct(int $order, string $opcode)
    {
        $this->order = $order;
        $this->opcode = $opcode;
        $this->arg_order = 1;
        $this->arg_count = 0;
        $this->arguments = [];
    }      

    // Get the order of the instruction
    public function get_order(): int {
        return $this->order;
    }

    // Get the opcode of the instruction
    public function get_opcode(): string {
        return $this->opcode;
    }

    /**
     * @return Argument[]
     */
    public function get_args(): array {
        return $this->arguments;
    }

    // Handle the argument of the instruction
    public function handleArgument(\DOMNode $argument): void
    {
        if (!$argument instanceof \DOMElement)
            throw new InvalidSourceException();

        // Validate the argument tag name
        $arg_order = null;
        if (preg_match("/^arg\d+$/", $argument->tagName))
            $arg_order = preg_replace("/arg/", "", $argument->tagName);
        else
            throw new InvalidSourceException();

        // Validate the argument order
        if ((intval($arg_order) <= 0) || empty($arg_order))
            throw new InvalidSourceException();

        // Create the argument object
        $value = trim((string)$argument->nodeValue);
        $type = $argument->getAttribute("type"); 
        switch($type){
            case "type":
                $arg_tmp = new Type("type", intval($arg_order), $value);
                break;
                
            case "label":
                $arg_tmp = new Label("label", intval($arg_order), $value);
                break;

            case "var":
                if (strpos($value, "@")){
                    $value = explode('@', $value);  
                    $arg_tmp = new Variable("var", intval($arg_order), $value[0], $value[1]);
                } else {
                    throw new InvalidSourceException();
                }
                break;

            case "int":
            case "bool":
            case "string":
            case "nil":
                if (strpos($value, "@")){
                    $value = explode('@', $value);
                    if ($value[0] == "GF" || $value[0] == "LF" || $value[0] == "TF")  
                        $arg_tmp = new Constant($type, intval($arg_order), $value[0], $value[1]);
                    else 
                        $arg_tmp = new Constant($type, intval($arg_order), null, $value[0] . '@' . $value[1]);
                } else {
                    $arg_tmp = new Constant($type, intval($arg_order), null, $value);
                }
                break;

            default:
                throw new InvalidSourceException();
        }

        // Add the argument to the array
        $this->arguments[] = $arg_tmp;
        $this->arg_count++;
    }

    // Sort the arguments by their order
    public function sort_arguments(): void {
        // Sort the arguments by their order
        for ($i = 0; $i < count($this->arguments); $i++){
            for ($j = 0; $j < count($this->arguments) - 1; $j++){
                if ($this->arguments[$j]->get_order() > $this->arguments[$j + 1]->get_order()){
                    $tmp = $this->arguments[$j];
                    $this->arguments[$j] = $this->arguments[$j + 1];
                    $this->arguments[$j + 1] = $tmp;
                } else if ($this->arguments[$j]->get_order() === $this->arguments[$j + 1]->get_order()) {
                    throw new InvalidSourceException();
                }
            }
        }

        $correct_order = 1;

        // Check if the arguments order is correct
        foreach ($this->arguments as $argument) {
            if (intval($argument->get_order()) !== $correct_order)
                throw new InvalidSourceException();
            $correct_order++;
        }
    }

    // Check if the symbol is valid
    protected function check_symb(string $constant_type): bool {
        return ($constant_type === "int" || $constant_type === "bool" || $constant_type === "string" || $constant_type === "nil" || $constant_type === "var") ? true : false;
    }

    abstract public function execute(Interpret $interpret): void;

    // Check if the types are valid
    protected function valid_types(string $arg_type, string &$value): bool {
        // Check if the value is of correct type
        if ($arg_type === "int" && filter_var($value, FILTER_VALIDATE_INT) === false)
            return false;
        else if ($arg_type === "bool" && !($value === "true" || $value === "false"))
            return false;
        else if ($arg_type === "string" && !preg_match('/^([^#\\\\]|\\\\[0-9]{3})*$/', $value))
            return false;
        else if ($arg_type === "nil" && !($value === "nil"))
            return false;

        // Convert string
        if ($arg_type === "string") {
            if (preg_match_all('/\\\\(0[0-9]{2}|0[0-2][0-9]|035|092)/', $value, $matches)) {
                foreach ($matches[1] as $match) {
                    $char = chr(intval($match));
                    $value = str_replace('\\' . $match, $char, $value);
                }
            }
        }

        return true;
    }

    // Get the value of the argument
    protected function get_value(string $arg_type, string $arg_value, ?string $arg_frame, Interpret $interpret): ?string  {  
        if ($arg_type === "var"){
            // Check the frame type
            switch($arg_frame) {
                case "GF":
                    if (!empty($interpret->global_frame)){
                        foreach ($interpret->global_frame as $variableName => $variableData) {
                            if ($arg_value === $variableName)
                                return $variableData["value"];
                        }
                        // The variable doesn't exist
                        throw new VariableAccessException();
                    } else {
                        // The variable doesn't exist
                        throw new VariableAccessException();
                    }
                case "LF":
                    // Check if the local frame is defined
                    if ($interpret->frame_stack->isNull())
                        throw new FrameAccessException();

                    if (!$interpret->frame_stack->isEmpty()) {
                        foreach ($interpret->frame_stack->top() as $variableName => $variableData){
                            if ($arg_value === $variableName)
                                return $variableData["value"];
                        }
                        // The variable doesn't exist
                        throw new VariableAccessException();
                    } else {
                        // The variable doesn't exist
                        throw new VariableAccessException();
                    }
                case "TF":
                    // Check if the temporary frame is defined
                    if ($interpret->temporary_frame === null)
                        throw new FrameAccessException();

                    if (!empty($interpret->temporary_frame)){
                        foreach ($interpret->temporary_frame as $variableName => $variableData){
                            if ($arg_value === $variableName)
                                return $variableData["value"];
                        }
                        // The variable doesn't exist
                        throw new VariableAccessException();
                    } else {
                        // The variable doesn't exist
                        throw new VariableAccessException();
                    }
                default:
                    // Invalid frame type
                    throw new InvalidSourceException();
            }
        } else {
            return $arg_value;
        }
    }

    // Get the type of the argument
    protected function get_type(string $arg_type, string $arg_value, ?string $arg_frame, Interpret $interpret): ?string  {  
        if ($arg_type === "var"){
            // Check the frame type
            switch($arg_frame) {
                case "GF":
                    if (!empty($interpret->global_frame)){
                        foreach ($interpret->global_frame as $variableName => $variableData){
                            if ($arg_value === $variableName)
                                return $variableData["type"];
                        }
                        // The variable doesn't exist
                        throw new VariableAccessException();
                    } else {
                        // The variable doesn't exist
                        throw new VariableAccessException();
                    }
                case "LF":
                    // Check if the local frame is defined
                    if ($interpret->frame_stack->isNull())
                        throw new FrameAccessException();

                    if (!$interpret->frame_stack->isEmpty()) {
                        foreach ($interpret->frame_stack->top() as $variableName => $variableData){
                            if ($arg_value === $variableName)
                                return $variableData["type"];
                        }
                        // The variable doesn't exist
                        throw new VariableAccessException();
                    } else {
                        // The variable doesn't exist
                        throw new VariableAccessException();
                    }
                case "TF":
                    // Check if the temporary frame is defined
                    if ($interpret->temporary_frame === null)
                        throw new FrameAccessException();

                    if (!empty($interpret->temporary_frame)){
                        foreach ($interpret->temporary_frame as $variableName => $variableData){
                            if ($arg_value === $variableName)
                                return $variableData["type"];
                        }
                        // The variable doesn't exist
                        throw new VariableAccessException();
                    } else {
                        // The variable doesn't exist
                        throw new VariableAccessException();
                    }
                default:
                    // Invalid frame type
                    throw new InvalidSourceException();
            }
        } else {
            return $arg_type;
        }
    }

    // Set the value of the argument
    protected function set_value(string $arg_value, mixed $value, ?string $arg_frame, Interpret $interpret): void {
        // Check the frame type
        switch($arg_frame){
            case "GF":
                if (!empty($interpret->global_frame)){
                    foreach ($interpret->global_frame as $variableName => $variableData){
                        if ($arg_value === $variableName) {
                            $interpret->global_frame[$variableName]["value"] = $value;
                            return;
                        }
                    }
                } else {
                    // The variable doesn't exist
                    throw new VariableAccessException();
                }
            case "LF":
                // Check if the local frame is defined
                if ($interpret->frame_stack->isNull())
                    throw new FrameAccessException();
    
                if (!$interpret->frame_stack->isEmpty()) {
                    $top_item = $interpret->frame_stack->pop(); 
                    foreach ($top_item as $variableName => $variableData){
                        if ($arg_value === $variableName) {
                            $top_item[$variableName]["value"] = $value;
                            $interpret->frame_stack->push($top_item);
                            return;
                        }
                    }
                } else {
                    // The variable doesn't exist
                    throw new VariableAccessException();
                }
                break;
            case "TF":
                // Check if the temporary frame is defined
                if ($interpret->temporary_frame === null)
                    throw new FrameAccessException();

                if (!empty($interpret->temporary_frame)){
                    foreach ($interpret->temporary_frame as $variableName => $variableData){
                        if ($arg_value === $variableName) {
                            $interpret->temporary_frame[$variableName]["value"] = $value;
                            return;
                        }
                    }
                } else {
                    // The destination variable doesn't exist
                  throw new VariableAccessException();
                }
                default:
                    // Invalid frame type
                    throw new InvalidSourceException();
            }
            // The destination variable doesn't exist
            throw new SemanticException();
    }

    // Set the type of the argument
    protected function set_type(string $arg_value, mixed $type, ?string $arg_frame, Interpret $interpret): void {
        // Check the frame type
        switch($arg_frame){
            case "GF":
                if (!empty($interpret->global_frame)){
                    foreach ($interpret->global_frame as $variableName => $variableData){
                        if ($arg_value === $variableName) {
                            $interpret->global_frame[$variableName]["type"] = $type;
                            return;
                        }
                    }
                } else {
                    // The variable doesn't exist
                    throw new VariableAccessException();
                }
            case "LF":
                // Check if the local frame is defined
                if ($interpret->frame_stack->isNull())
                    exit(55);
    
                if (!$interpret->frame_stack->isEmpty()) {
                    $top_item = $interpret->frame_stack->pop(); 
                    foreach ($top_item as $variableName => $variableData){
                        if ($arg_value === $variableName) {
                            $top_item[$variableName]["type"] = $type;
                            $interpret->frame_stack->push($top_item);
                            return;
                        }
                    }
                } else {
                    // The variable doesn't exist
                    throw new VariableAccessException();
                }
                break;
            case "TF":
                // Check if the temporary frame is defined
                if ($interpret->temporary_frame === null)
                   exit(55);

                if (!empty($interpret->temporary_frame)){
                    foreach ($interpret->temporary_frame as $variableName => $variableData){
                        if ($arg_value === $variableName) {
                            $interpret->temporary_frame[$variableName]["type"] = $type;
                            return;
                        }
                    }
                } else {
                    // The variable doesn't exist
                    throw new VariableAccessException();
                }
            default:
                // Invalid frame type
                throw new InvalidSourceException();
        }
        // The variable doesn't exist
        throw new VariableAccessException();
    }

}