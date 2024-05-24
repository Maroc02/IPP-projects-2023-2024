<?php
/**
 * @author Marek Čupr (xcuprm01)
 * version 1.0
 */

namespace IPP\Student;

/**
 * Class FrameStack
 * Represents a stack of frames
 */
class FrameStack {
    /**
     * @var array<string, mixed>[]
     */
    private $frame_stack;

    public function __construct() {
        $this->frame_stack = [];
    }

    /**
    * @param array<string, mixed>|null $frame 
    */
    public function push(?array $frame): void {
        array_push($this->frame_stack, $frame);
    }

    /**
    * @return array<string, mixed>
    */
    public function pop(): array {
        if ($this->peek() == null)
            throw new FrameAccessException();
        
        return array_pop($this->frame_stack);
    }

    /**
    * @return array<string, mixed>
    */
    public function top(): array {
        if ($this->peek() == null)
            throw new FrameAccessException();
        
        return end($this->frame_stack);
    }

    /**
     * @return int|null
     */
    public function peek(): ?int {
        $count = count($this->frame_stack);
        return ($count > 0) ? $count : null;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool {
        return empty($this->top());
    }
    
    /**
     * @return bool
     */
    public function isNull(): bool {
        return ($this->peek() == null) ? true : false;
    }

    /**
     * @return int
     */
    public function size(): int {
        return count($this->frame_stack);
    }
} 
