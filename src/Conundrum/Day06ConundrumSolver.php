<?php

declare(strict_types=1);

namespace App\Conundrum;

// /// Day 6: Wait For It ///
class Day06ConundrumSolver extends AbstractConundrumSolver
{
    private array $winningCombinationsArray = [];
    private int $winningCombinations = 0;

    public function __construct(string $folder)
    {
        parent::__construct($folder, keepAsString: true);
    }

    public function prepare(): void
    {
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): mixed
    {
        preg_match_all('/\d+/', $this->getInput(), $matches, PREG_SET_ORDER);
        array_walk($matches, function (&$match) {
            $match = $match[0];
        });

        [$times, $records] = array_chunk($matches, count($matches) / 2);

        $this->computeWinningCombinations($times, $records);

        return array_product($this->winningCombinationsArray);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): mixed
    {
        $input = explode(PHP_EOL, $this->getInput());
        array_walk($input, function (string &$match) {
            $match = filter_var($match, FILTER_SANITIZE_NUMBER_INT);
        });

        [$time, $record] = $input;

        $this->computeWinningCombinations([$time], [$record]);

        return $this->winningCombinations;
    }

    ////////////////
    // METHODS
    ////////////////

    private function computeWinningCombinations(array $times, array $records): void
    {
        foreach ($times as $key => $time) {
            $this->winningCombinations = 0;
            $best = $records[$key];

            for ($held = 1; $held <= $time; $held++) {
                $remaining = $time - $held;

                if ($best < ($held * $remaining)) {
                    $this->winningCombinations++;
                }
            }

            if (0 < $this->winningCombinations) {
                $this->winningCombinationsArray[] = $this->winningCombinations;
            }
        }
    }
}
