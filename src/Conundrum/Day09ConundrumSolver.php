<?php

declare(strict_types=1);

namespace App\Conundrum;

// /// Day 9: Mirage Maintenance ///
use App\Entity\Day09\History;
use App\Entity\Day09\Sequence;

class Day09ConundrumSolver extends AbstractConundrumSolver
{
    private array $firstValues = [];
    private array $lastValues = [];

    public function __construct(string $folder)
    {
        parent::__construct($folder);
    }

    public function prepare(): void
    {
        foreach ($this->getInput() as $line) {
            $history = new History();

            $history->computeSequences(array_map('intval', explode(' ', $line)));

            $firstValue = $lastValue = 0;

            /** @var Sequence $sequence */
            foreach ($history->sequences as $sequence) {
                $sequence->lastValue += $lastValue;
                $lastValue = $sequence->lastValue;

                $sequence->firstValue -= $firstValue;
                $firstValue = $sequence->firstValue;
            }

            $firstSequence = end($history->sequences);
            $this->lastValues[] = $firstSequence->lastValue;
            $this->firstValues[] = $firstSequence->firstValue;
        }
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): mixed
    {
        return array_sum($this->lastValues);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): mixed
    {
        return array_sum($this->firstValues);
    }

    ////////////////
    // METHODS
    ////////////////

}
