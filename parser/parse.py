''' ******************************************************* parser.py ******************************************************** */
/*  Author: Marek Čupr (xcuprm01)                                                                                              */
/*  Subject: IPP - Project                                                                                                     */
/*  Date: 7. 2. 2024                                                                                                           */
/*  Functionality: Checks the lexical and syntactic correctness of the code and creates an XML representation of the program   */
/* ************************************************************************************************************************** '''

import sys
import re
import xml.dom.minidom as md

# Print the usage
def print_usage():
    print("Usage: The script reads a source code 'IPP-code24' from stdin, checks the lexical and syntactic correctness of the code and creates an XML representation of the program to stdout.\n")
    print("How to run: python3.10 parser.py [OPTIONS] < filename")
    print("\t[OPTIONS] = --help")
    print("\t\t... prints the usage message")

# Check the program arguments validity  
def check_args(args):
    if len(args) > 2:
        print('Invalid argument count!', file=sys.stderr)
        exit(10)

    if len(args) == 2:
        if args[1] == "--help":
            print_usage()
            exit(0)
        else:
            print('Invalid argument count!', file=sys.stderr)
            exit(10)

# XML class to generate the output code
class XML:
    # Constructor
    def __init__(self):
        # Initialize the XML
        self.instructionCnt = 1 # Instruction order
        self.generate_root()

    # Generate the XML header
    def generate_root(self):
        self.xml_root = md.Document()
        program_element = self.xml_root.createElement("program")
        program_element.setAttribute("language", "IPPcode24")
        self.xml_root.appendChild(program_element)

    # Generate an XML instruction
    def generate_instruction(self, line, type):
        instruction_element = self.xml_root.createElement("instruction")
        instruction_element.setAttribute("order", str(self.instructionCnt)) # Order
        instruction_element.setAttribute("opcode", line[0].upper()) # Opcode

        for argument in range(1, len(line)): # Handle opcode arguments
            arg_element = self.xml_root.createElement(f'arg{argument}')
            arg_element.setAttribute("type", type[argument - 1])
            arg_element.appendChild(self.xml_root.createTextNode(line[argument]))
            instruction_element.appendChild(arg_element)

        # Append the instruction to the XML document
        self.xml_root.documentElement.appendChild(instruction_element)
        self.instructionCnt += 1

    # Generate the final XML representation and print it to stdout
    def generate_string(self):
        xml_str = self.xml_root.toprettyxml(indent="    ", encoding="UTF-8")
        print(xml_str.decode("utf-8"))

# Parser class
class Parser:
    # Constructor
    def __init__(self):
        # Initialize the parser
        self.xml = XML() # Create new XML object
        self.type=[] # Empty list to hold the operand types later 
        # Regex patterns
        self.var_pattern = r'^(LF|TF|GF)@[-_$&%*!?a-zA-Z][-_$&%*!?a-zA-Z0-9]*$'
        self.symb_pattern = r'^((LF|TF|GF)@[-_$&%*!?a-zA-Z][-_$&%*!?a-zA-Z0-9]*)|(int@[+-]?([0-9]+|0[oO]{1}[0-7]+|0[xX]{1}[0-9a-fA-F]+)|bool@(true|false)|nil@nil|string@(([^#\s\\]|\\\d{3})+)*)$'
        self.type_pattern = r'^(int|string|bool)'
        self.label_pattern = r'^[-_$&%*!?a-zA-Z][-_$&%*!?a-zA-Z0-9]*$'

    # Run the main program logic
    def parse(self):
        self.check_header()
        self.handle_instructions()
        self.xml.generate_string()

    # Locate the header
    def check_header(self):
        for line in sys.stdin:
            line = self.cut_comment(line)
            if line:
                if line[0].upper() == ".IPPCODE24": # The header was located
                    return 
                else:
                    exit(21)
        # The header is missing
        exit(21)

    # Remove all the line comments and unnecessary white spaces
    def cut_comment(self, line):
        cut_comments = line.split('#')[0].strip() # Delete the line comments
        return cut_comments.split() # Cut all the unnecessary white spaces

    # Handle all the instructions based on their opcode
    def handle_instructions(self):
        for line in sys.stdin:
            line = self.cut_comment(line)
            if line:
                match line[0].upper():
                    # Instructions with no operands
                    case "CREATEFRAME" | "PUSHFRAME" | "POPFRAME" | "RETURN" | "BREAK":
                        if (len(line) != 1): # Wrong operands count
                            exit(23)

                    # Instructions with one operand ... [INSTRUCTION] <var>
                    case "DEFVAR" | "POPS":
                        if (len(line) != 2): # Wrong operands count
                            exit(23)
                        # Check operands
                        self.check_var(line[1])

                    # Instructions with one operand ... [INSTRUCTION] <symb>
                    case "PUSHS" | "WRITE" | "EXIT" | "DPRINT":
                        if (len(line) != 2): # Wrong operands count
                            exit(23)
                        # Check operands
                        line[1] = self.check_symb(line[1]) 

                    # Instructions with one argument ... [INSTRUCTION] <label>
                    case "CALL" | "LABEL" | "JUMP":
                        if (len(line) != 2): # Wrong operands count
                            exit(23)
                        # Check operands
                        self.check_label(line[1])

                    # Instructions with two arguments ... [INSTRUCTION] <var> <symb>
                    case "MOVE" | "INT2CHAR" | "STRLEN" | "TYPE" | "NOT" : 
                        if (len(line) != 3): # Wrong operands count
                            exit(23)
                        # Check operands
                        self.check_var(line[1])
                        line[2] = self.check_symb(line[2]) 

                    # Instructions with two arguments ... [INSTRUCTION] <var> <type>
                    case "READ":
                        if (len(line) != 3): # Wrong operands count
                            exit(23)
                        # Check operands
                        self.check_var(line[1])
                        self.check_type(line[2])

                    # Instructions with three arguments ... [INSTRUCTION] <var> <symb1> <symb2>
                    case "ADD" | "SUB" | "MUL" | "IDIV" | "LT" | "GT" | "EQ" | "AND" | "OR" | "STRI2INT" | "CONCAT" | "GETCHAR" | "SETCHAR":
                        if (len(line) != 4): # Wrong operands count
                            exit(23)
                        # Check operands
                        self.check_var(line[1])
                        line[2] = self.check_symb(line[2]) 
                        line[3] = self.check_symb(line[3]) 

                    # Instructions with three arguments ... [INSTRUCTION] <label> <symb1> <symb2>
                    case "JUMPIFEQ" | "JUMPIFNEQ":
                        if (len(line) != 4): # Wrong operands count
                            exit(23)
                            # Check operands
                        self.check_label(line[1])
                        line[2] = self.check_symb(line[2]) 
                        line[3] = self.check_symb(line[3]) 
                    
                    # Invalid opcode
                    case _:
                        if line[0].upper().isalnum():
                            exit(22)
                        else:
                            exit(23)

                # Generate the instruction
                self.xml.generate_instruction(line, self.type)
                self.type.clear()
                
    # Check the var syntax
    def check_var(self, var):
        # Match with the regex pattern
        reg_result = re.search(self.var_pattern, var)
        if not reg_result:
            exit(23)
        # Append operand the type
        self.type.append("var")

    # Checks the symb syntax
    def check_symb(self, symb):
        # Match with the regex pattern
        reg_result = re.search(self.symb_pattern, symb)
        if not reg_result:
            exit(23)
        # Append operand the type
        return self.handle_symb(symb)

    # Checks the label syntax
    def check_label(self, label):
        # Match with the regex pattern
        reg_result = re.search(self.label_pattern, label)
        if not reg_result:
            exit(23)
        # Append operand the type
        self.type.append("label")

    # Checks the type syntax
    def check_type(self, type):
        # Match with the regex pattern
        reg_result = re.search(self.type_pattern, type)
        if not reg_result:
            exit(23)
        # Append operand the type
        self.type.append("type")

    # Handle the symbol type
    def handle_symb(self, symb):
        if "@" in symb:
            left , right = symb.split("@", 1)
            if left == "string" or left == "int" or left == "bool" or left == "nil":
                self.type.append(left)
                return right
        self.type.append("var")
        return symb

# Program Start
if __name__ == "__main__":
    # Check the program arguments
    check_args(sys.argv)
    # Create a Parser object
    parser = Parser()
    # Run the parser
    parser.parse()
