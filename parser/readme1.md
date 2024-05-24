# Documentation of IPP Task 1 Implementation 2023/2024

**Name and Surname:** Marek Čupr  
**Login:** xcuprm01

## Arguments Handling

Instead of implementing a separate class or utilizing argparse, the script uses a simple if statement for argument validation since only one optional parameter, `--help`, is accepted by the script.

## Main Logic

The core functionality of the program revolves around two primary classes: `XML` and `Parser`. The `XML` class is responsible for generating the XML representation of the program, while the `Parser` class processes the input code and ensures its correctness.

### XML class

The `XML` class makes use of the `xml.dom.minidom` library to create and manipulate the XML document. It provides methods for generating the root element, individual instructions, and the final XML representation. All instructions are gradually added to the XML document and printed at the end of the program.

### Parser class

The `Parser` class encapsulates the primary logic of the script. Its methods handle header checking, instruction processing, and operand validation. Various regex patterns are used for operands validation.

## Program Flow

1. Program arguments are validated using the `check_args` function.
2. Instances of the `Parser` and `XML` classes are created.
3. The XML root element is generated using the `generate_root` method.
4. The presence of the ".IPPcode24" header is checked.
5. Line comments and unnecessary white spaces are removed.
6. Instructions are categorized by opcode using a loop, and their operands are validated using regular expressions.
7. Valid instructions are systematically added to the XML document.
8. The final XML representation is generated, converted to a formatted string, and printed to stdout.