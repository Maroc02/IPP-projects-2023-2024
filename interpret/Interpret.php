<?php
/**
 * @author Marek Čupr (xcuprm01)
 * @version 1.0
 */

namespace IPP\Student;

/**
 * Class Constant
 * Encapsulates the main logic of the program
 */
class Interpret {
    /**
     * @var Instruction[] Array of all the instructions
     */
    public $instructions; 
    
    /**
     * @var \DOMDocument The input XML document
     */
    private $dom; 
    
    /**
     * @var \IPP\Core\Interface\InputReader The standard input reader
     */
    private $input;

    /**
     * @var \IPP\Core\Interface\OutputWriter The standard output writer
     */
    private $stdout;

    /**
     * @var \IPP\Core\Interface\OutputWriter The standard error writer
     */
    private $stderr;
    
    /**
     * @var array<string, mixed> The global frame
     */
    public $global_frame; 
    
    /**
     * @var array<string, mixed>|null The local frame
     */
    public $local_frame;
    
    /**
     * @var array<string, mixed>|null The temporary frame
     */
    public $temporary_frame; 

    /**
     * @var array<string> The labels
     */
     private $labels;

    /**
     * @var array<int> The call stack
     */
     public $call_stack;

    /**
     * @var int The instruction iterator
     */
    public $instructions_it; 
    
    /**
     * @var FrameStack The frame stack
     */
    public $frame_stack;

    /**
     * @var array<mixed> The data frame
     */
    public $data_frame;

    public function __construct(\DOMDocument $dom, \IPP\Core\Interface\InputReader $input, \IPP\Core\Interface\OutputWriter $stdout, \IPP\Core\Interface\OutputWriter $stderr)
    {
        $this->instructions = [];
        $this->global_frame = [];
        $this->temporary_frame = null;
        $this->local_frame = null;
        $this->labels = [];
        $this->frame_stack = new FrameStack;
        $this->data_frame = [];
        $this->call_stack = [];
        $this->instructions_it = 0;
        $this->dom = $dom;
        $this->input = $input;
        $this->stdout = $stdout;
        $this->stderr = $stderr;
    }
    
    // Main program logic
    public function run(): void {
        // Validate the XML
        $this->check_XML($this->dom);

        // Sort the instructions by order
        $this->sort_instructions();
        
        // Save all the labels
        $this->save_labels();
        
        // Execute the instructions
        $this->executeInstructions();

        exit(0);
    }

    // Validate the XML input
    private function check_XML(\DOMDocument $dom): void {
        // Validate the XML header
        $programHeader = $dom->documentElement; 
        if ($programHeader->tagName !== 'program' || $programHeader->getAttribute('language') !== 'IPPcode24')
            throw new InvalidSourceException();

        // Check other header attributes
        foreach ($programHeader->attributes as $attribute) {
            // Check if the attribute name is 'language'
            if (($attribute->name !== "language") && ($attribute->name !== "name") && ($attribute->name !== "description"))
                throw new InvalidSourceException();
        }
    
        // Validate the XML instructions
        foreach($programHeader->childNodes as $childNode){
            if ($childNode instanceof \DOMElement) {
                // Check the tag name
                if ($childNode->tagName !== 'instruction')
                    throw new InvalidSourceException();
            
                // Get the instruction opcode and order
                $order = intval($childNode->getAttribute('order')); // Convert to number
                $opcode = $childNode->getAttribute('opcode');

                // Validate the order and opcode 
                if ((is_numeric($order)) && ($order > 0) && (!empty($opcode))){
                    // Create new Instruction object
                    $instruction = $this->create_instruction($order, $opcode);
                    // Handle instruction arguments
                    foreach($childNode->childNodes as $argument){
                        if ($argument instanceof \DOMElement)
                            $instruction->handleArgument($argument);
                    }
                    // Sort and validate the instruction arguments
                    $instruction->sort_arguments();
                    // Add the instruction to the instructions array
                    $this->instructions[] = $instruction;
                } else { // Invalid order or opcode
                    throw new InvalidSourceException();
                }
            }
        }
    }

    // Sort the instructions by order
    private function sort_instructions(): void {
        for ($i = 0; $i < count($this->instructions); $i++){
            for ($j = 0; $j < count($this->instructions) - 1; $j++){
                if ($this->instructions[$j]->get_order() > $this->instructions[$j + 1]->get_order()){
                    // Swap the instructions
                    $tmp = $this->instructions[$j];
                    $this->instructions[$j] = $this->instructions[$j + 1];
                    $this->instructions[$j + 1] = $tmp;
                } else if ($this->instructions[$j]->get_order() === $this->instructions[$j + 1]->get_order()) {
                    // The order is not unique 
                    throw new InvalidSourceException();
                }
            }
        }
    }

    // Save all the labels
    private function save_labels(): void {
        $i = 0; // Associate index with each label
        foreach ($this->instructions as $instruction) {
            if ($instruction->get_opcode() === "LABEL"){
                foreach ($instruction->get_args() as $arg){
                    if ($arg->get_type() === "label") {
                        // Get the label name
                        $label = $arg->get_label();
                        // Check if the label is unique
                        if (!array_key_exists($label, $this->labels))
                            $this->labels += [$label => $i];
                        else
                            throw new SemanticException();
                   }
                }
            }
            $i++;
        }
    }

    // Create new Instruction based on the opcode
    private function create_instruction(int $order, string $opcode): Instruction {
        switch(strtoupper($opcode)){
            case "MOVE":
                return new MoveInstruction($order, $opcode);
                
            case "CREATEFRAME":
                return new CreateFrameInstruction($order, $opcode);

            case "PUSHFRAME":
                return new PushFrameInstruction($order, $opcode);

            case "POPFRAME":
                return new PopFrameInstruction($order, $opcode);

            case "DEFVAR":
                return new DefVarInstruction($order, $opcode);
                
            case "CALL":
                return new CallInstruction($order, $opcode);
                
            case "RETURN":
                return new ReturnInstruction($order, $opcode);

            case "PUSHS":
                return new PushsInstruction($order, $opcode);

            case "POPS":
                return new PopsInstruction($order, $opcode);

            case "ADD":
                return new AddInstruction($order, $opcode);

            case "SUB":
                return new SubInstruction($order, $opcode);

            case "MUL":
                return new MulInstruction($order, $opcode);

            case "IDIV":
                return new IDivInstruction($order, $opcode);

            case "LT":
                return new LTInstruction($order, $opcode);

            case "GT":
                return new GTInstruction($order, $opcode);

            case "EQ":
                return new EQInstruction($order, $opcode);

            case "AND":
                return new AndInstruction($order, $opcode);

            case "OR":
                return new OrInstruction($order, $opcode);
                
            case "NOT":
                return new NotInstruction($order, $opcode);

            case "INT2CHAR":
                return new Int2CharInstruction($order, $opcode);

            case "STRI2INT":
                return new StrI2IntInstruction($order, $opcode);

            case "READ":
                return new ReadInstruction($order, $opcode);

            case "WRITE":
                return new WriteInstruction($order, $opcode);
 
            case "CONCAT":
                return new ConcatInstruction($order, $opcode);

            case "STRLEN":
                return new StrlenInstruction($order, $opcode);

            case "GETCHAR":
                return new GetcharInstruction($order, $opcode);

            case "SETCHAR":
                return new SetcharInstruction($order, $opcode);

            case "TYPE":
                return new TypeInstruction($order, $opcode);

            case "LABEL":
                return new LabelInstruction($order, $opcode);

            case "JUMP":
                return new JumpInstruction($order, $opcode);

            case "JUMPIFEQ":
                return new JumpIFEQInstruction($order, $opcode);

            case "JUMPIFNEQ":
                return new JumpIFNEQInstruction($order, $opcode);

            case "EXIT":
                return new ExitInstruction($order, $opcode);

            case "DPRINT":
                return new DPrintInstruction($order, $opcode);

            case "BREAK":
                return new BreakInstruction($order, $opcode);
                
            default:
                throw new InvalidSourceException();
        }
    }
    
    // Get the standard input reader.
    public function get_stdin(): \IPP\Core\Interface\InputReader {
        return $this->input;
    }

    // Get the standard input reader.
    public function get_stdout(): \IPP\Core\Interface\OutputWriter {
        return $this->stdout;
    }

    // Get the standard output writer.
    public function get_stderr(): \IPP\Core\Interface\OutputWriter {
        return $this->stderr;
    }

    /**
     * Get the labels.
     *
     * @return string[] Array of labels.
     */
    public function get_labels(): array {
        return $this->labels;
    }

    // Execute each instruction
    private function executeInstructions(): void {
        while($this->instructions_it < count($this->instructions))
            $this->instructions[$this->instructions_it]->execute($this);
    }
}