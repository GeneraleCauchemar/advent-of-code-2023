<?php

declare(strict_types=1);

namespace App\Conundrum;

// /// Day 11: Cosmic Expansion ///

use App\Entity\Day11\Position;

class Day11ConundrumSolver extends AbstractConundrumSolver
{
    private array $universe = [];
    private array $galaxiesBeforeExpansion = [];
    private array $galaxiesAfterExpansion = [];
    private array $pairs = [];

    public function __construct(string $folder)
    {
        parent::__construct($folder);
    }

    public function prepare(): void
    {
        $this->mapBeforeExpansion();
        $this->expand();
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
        $sum = 0;

        foreach ($this->pairs as $pair) {
            $pair = $this->computeNewPositions(...$pair);
            $sum += $this->getManhattan(...$pair);
        }

        return $sum;
    }

    ////////////////
    // METHODS
    ////////////////

    private function mapBeforeExpansion(): void
    {
        $i = 1;

        foreach ($this->getInput() as $row => $line) {
            $galaxies = array_filter(str_split($line), fn(string $symbol) => '#' === $symbol);

            foreach ($galaxies as $column => $galaxy) {
                $this->galaxiesBeforeExpansion[$i] = new Position($row, $column, $i++);
            }
        }
    }

    private function expand(): void
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
            $lines = array_fill(0, $this->getExpansionFactor($line), $line);
            $expanded = array_merge($expanded, $lines);
        }

        return $expanded;
    }

    private function getExpansionFactor(array $values): int
    {
        return $this->isOnlyEmptySpace($values) ? 1 : 2;
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
        $i = 1;

        foreach ($this->universe as $row => $line) {
            $galaxies = array_filter($line, fn(string $symbol) => '#' === $symbol);

            foreach ($galaxies as $column => $galaxy) {
                $this->galaxiesAfterExpansion[$i++] = new Position($row, $column, $i);
            }
        }
    }

    private function createPairs(): void
    {
        for ($i = 0; $i < count($this->galaxiesAfterExpansion); $i++) {
            for ($j = $i + 1; $j < count($this->galaxiesAfterExpansion); $j++) {
                $this->pairs[] = [
                    $this->galaxiesAfterExpansion[$i + 1],
                    $this->galaxiesAfterExpansion[$j + 1],
                ];
            }
        }
    }

    private function getManhattan(Position $from, Position $to): int
    {
        return abs($from->column - $to->column) + abs($from->row - $to->row);
    }

    private function computeNewPositions(Position $from, Position $to): array
    {
        $beforeFrom = $this->galaxiesBeforeExpansion[$from->id];
        $beforeTo = $this->galaxiesBeforeExpansion[$to->id];

        return [
            new Position(
                $this->computeDiff($beforeFrom->row, $from->row),
                $this->computeDiff($beforeFrom->column, $from->column),
                $from->id
            ),
            new Position(
                $this->computeDiff($beforeTo->row, $to->row),
                $this->computeDiff($beforeTo->column, $to->column),
                $from->id
            ),
        ];
    }

    private function computeDiff(int $before, int $after): int
    {
        $diff = abs($after - $before);

        return $before + ($diff * 1000000 - $diff);
    }
}
