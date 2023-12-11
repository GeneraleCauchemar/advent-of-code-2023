<?php

declare(strict_types=1);

namespace App\Conundrum;

// /// Day 11: Cosmic Expansion ///

use App\Entity\Day11\Position;

class Day11ConundrumSolver extends AbstractConundrumSolver
{
    private array $universe = [];
    private array $galaxies = [];
    private array $pairs = [];

    public function __construct(string $folder)
    {
        parent::__construct($folder);
    }

    public function prepare(): void
    {
        $this->expand($this->getInput());
        $this->pinpointGalaxies();
        $this->createPairs();
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): mixed
    {
        $sum = 0;

        foreach ($this->pairs as $pair) {
            $sum += $this->getManhattan(...$pair);
        }

        return $sum;
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): mixed
    {
        return self::UNDETERMINED;
    }

    ////////////////
    // METHODS
    ////////////////

    private function expand(array $input): void
    {
        // Expand lines then rotate to expand columns
        $input = $this->doExpand($this->getInput());
        $this->rotateMatrix($input);

        // Expand columns then rotate back
        $input = $this->doExpand($input);
        $this->rotateMatrix($input);

        $this->universe = $input;
    }

    private function doExpand(array $input): array
    {
        $expanded = [];

        foreach ($input as &$line) {
            $line = is_string($line) ? str_split($line) : $line;
            $expanded[] = $line;

            if ($this->isOnlyEmptySpace($line)) {
                $expanded[] = $line;
            }
        }

        return $expanded;
    }

    private function isOnlyEmptySpace(array $values): bool
    {
        return 1 === count(array_unique($values)) && '.' === array_unique($values)[0];
    }

    private function rotateMatrix(array &$matrix): void
    {
        // Effectively, this NULL callback loops through all the arrays in parallel taking each
        // value from them in turn to build a new array:
        // https://stackoverflow.com/a/30082922
        $matrix = array_map(null, ...$matrix);
    }

    private function pinpointGalaxies(): void
    {
        foreach ($this->universe as $row => $line) {
            $galaxies = array_filter($line, fn(string $symbol) => '#' === $symbol);

            foreach ($galaxies as $column => $galaxy) {
                $this->galaxies[] = new Position($row, $column);
            }
        }
    }

    private function createPairs(): void
    {
        for ($i = 0; $i < count($this->galaxies); $i++) {
            for ($j = $i + 1; $j < count($this->galaxies); $j++) {
                $this->pairs[] = [
                    $this->galaxies[$i],
                    $this->galaxies[$j],
                ];
            }
        }
    }

    private function getManhattan(Position $from, Position $to): int
    {
        return abs($from->column - $to->column) + abs($from->row - $to->row);
    }

    private function print(array $input): void
    {
        foreach ($input as $line) {
            echo implode('', $line) . "\n";
        }
    }
}
