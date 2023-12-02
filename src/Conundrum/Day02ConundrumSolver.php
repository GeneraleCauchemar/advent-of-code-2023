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

    public function __construct(string $folder)
    {
        parent::__construct($folder);
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): mixed
    {
        $valid = [];

        foreach ($this->getInput() as $line) {
            $invalidSubsets = [];

            foreach ($this->getSubsets($line) as $subset) {
                $invalidSubsets[] = array_filter(explode(', ', $subset), function (string $cubes) {
                    [$number, $color] = explode(' ', $cubes);

                    return self::AVAILABLE_CUBES[$color] < (int) $number;
                });
            }

            if (empty(array_filter($invalidSubsets))) {
                preg_match('/\s(\d+):/', $line, $matches);

                $valid[] = (int) $matches[1];
            }
        }

        return array_sum($valid);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): mixed
    {
        $sum = 0;

        foreach ($this->getInput() as $line) {
            $subsets = $this->getSubsets($line);
            $minCubes = array_fill_keys([self::RED, self::GREEN, self::BLUE], 0);

            foreach ($subsets as $subset) {
                array_map(function ($cubes) use (&$minCubes) {
                    [$number, $color] = explode(' ', $cubes);

                    if ($minCubes[$color] < (int) $number) {
                        $minCubes[$color] = (int) $number;
                    }
                }, explode(', ', $subset));
            }

            // Removing the empty colors and adding the power to the total sum
            $minCubes = array_filter($minCubes);
            $sum += array_product($minCubes);
        }

        return $sum;
    }

    private function getGame(string $input): string
    {
        return preg_replace('/Game\s(\d+): /', '', $input);
    }

    private function getSubsets(string $input): array
    {
        return explode('; ', $this->getGame($input));
    }
}
