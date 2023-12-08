<?php

namespace App\Entity\Day05;

class Range
{
    public function __construct(public int $from, public int $to, public int $operand = 0)
    {
    }

    public function convertToDestinationRange(): self
    {
        $this->from += $this->operand;
        $this->to += $this->operand;

        return $this;
    }
}
