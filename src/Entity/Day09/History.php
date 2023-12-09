<?php

namespace App\Entity\Day09;

class History
{
    public array $sequences;

    public function computeSequences(array $values): void
    {
        $sequence = $this->addSequence($values);

        do {
            $values = $sequence->computeNextSequenceValues($values);
            $sequence = $this->addSequence($values);
        } while (false === $sequence->isLast);

        $this->sequences = array_reverse($this->sequences);
    }

    public function addSequence(array $values): Sequence
    {
        $sequence = new Sequence($values);
        $this->sequences[] = $sequence;

        return $sequence;
    }
}
