<?php

declare(strict_types=1);

namespace App\Conundrum;

// /// Day 2: Cube Conundrum ///
class Day02ConundrumSolver extends AbstractConundrumSolver
{
    private const RED = 'red';
    private const GREEN = 'green';
    private const BLUE = 'blue';
    private const AVAILABLE_CUBES = [
        self::RED   => 12,
        self::GREEN => 13,
        self::BLUE  => 14,
    ];

    private array $validGames = [];
    private int $powerSum = 0;

    public function __construct(string $folder)
    {
        parent::__construct($folder);
    }

    public function prepare(): void
    {
        foreach ($this->getInput() as $line) {
            $hasImpossibleDraws = false;
            $minCubes = array_fill_keys([self::RED, self::GREEN, self::BLUE], 0);

            foreach ($this->getDraws($line) as $draw) {
                $this->computeDraw($draw, $hasImpossibleDraws, $minCubes);
            }

            // Keeping track of the impossible games IDs
            if (!$hasImpossibleDraws) {
                preg_match('/\s(\d+):/', $line, $matches);

                $this->validGames[] = (int) $matches[1];
            }

            // Removing the empty colors and adding the power to the total sum
            $minCubes = array_filter($minCubes);
            $this->powerSum += array_product($minCubes);
        }
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): mixed
    {
        return array_sum($this->validGames);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): mixed
    {
        return $this->powerSum;
    }

    ////////////////
    // METHODS
    ////////////////

    private function getDraws(string $input): array
    {
        return explode('; ', $this->getGame($input));
    }

    private function getGame(string $input): string
    {
        return preg_replace('/Game\s(\d+): /', '', $input);
    }

    private function computeDraw(string $draw, bool &$hasImpossibleDraws, array &$minCubes): void
    {
        array_map(function ($cubes) use (&$hasImpossibleDraws, &$minCubes) {
            [$number, $color] = explode(' ', $cubes);

            // More cubes of this color than available: impossible draw
            if (self::AVAILABLE_CUBES[$color] < (int) $number) {
                $hasImpossibleDraws = true;
            }

            // Min cubes of this color needed to make the draw possible
            if ($minCubes[$color] < (int) $number) {
                $minCubes[$color] = (int) $number;
            }
        }, explode(', ', $draw));
    }
}
