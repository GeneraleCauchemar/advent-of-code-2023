<?php

namespace App\Entity\Day07;

class Type
{
    public function __construct(
        public string $name,
        public int $weight,
        public int $differentCards,
        public int $maxRepetitions,
    ) {
    }

    public function isTypeOfHand(Hand $hand): bool
    {
        return count($hand->groupedCards) === $this->differentCards &&
            max($hand->groupedCards) === $this->maxRepetitions;
    }
}
