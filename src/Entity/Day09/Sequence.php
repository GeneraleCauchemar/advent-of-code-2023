<?php

namespace App\Entity\Day09;

class Sequence
{
    public int $firstValue = 0;
    public int $lastValue = 0;
    public bool $isLast = false;

    public function __construct(array $values)
    {
        $this->firstValue = $values[0];
        $this->lastValue = $values[count($values) - 1];
        $this->isLast = 1 === count(array_unique($values, SORT_NUMERIC)) && 0 === $values[0];
    }

    public function computeNextSequenceValues(array $values): array
    {
        $nextValues = [];

        array_reduce($values, function ($a, $b) use (&$nextValues) {
            if (null !== $a) {
                $nextValues[] = $b - $a;
            }

            return $b;
        });

        return $nextValues;
    }
}
