<?php

namespace App\Entity\Day08;

class Node
{
    public function __construct(
        public string $name,
        public string $left,
        public string $right,
    ) {
    }

    public function getInstruction(string $letter)
    {
        return $this->{'get' . $letter}();
    }

    public function getL(): string
    {
        return $this->left;
    }

    public function getR(): string
    {
        return $this->right;
    }
}
