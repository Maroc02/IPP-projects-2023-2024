<?php
/**
 * @brief Run the main logic of the program
 * @author Marek Čupr (xcuprm01)
 * @date 07.04.2024
 * @version 1.0
 */

namespace IPP\Student;

use IPP\Core\AbstractInterpreter;
use IPP\Core\Exception\NotImplementedException;

class Interpreter extends AbstractInterpreter
{
    public function execute(): int
    {
        // Get the DOM document
        $dom = $this->source->getDOMDocument();

        // Create new Interpret object
        $interpret = new Interpret($dom, $this->input, $this->stdout, $this->stderr);

        // Run the main logic
        $interpret->run();

        // Program success
        return 0;
    }
}