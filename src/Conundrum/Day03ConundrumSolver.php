<?php

declare(strict_types=1);

namespace App\Conundrum;

// /// Day 3: Gear Ratios ///
class Day03ConundrumSolver extends AbstractConundrumSolver
{
    private array $ints = [];
    private array $intPositions = [];
    private array $symbolPositions = [];
    private array $partNumbers = [];

    public function __construct(string $folder)
    {
        parent::__construct($folder);
    }

    public function prepare(): void
    {
        $intOffset = 0;

        foreach ($this->getInput() as $y => $line) {
            preg_match_all('/\d+|[^\d.]/', $line, $matches, PREG_OFFSET_CAPTURE);

            if (0 < $matches[0]) {
                foreach ($matches[0] as $match) {
                    if (is_numeric($match[0])) {
                        // Keep track of all ints found
                        $this->ints[$intOffset] = $match[0];
                        // For line Y, writes down every X coordinate and the associated int offset
                        $this->intPositions[$y] = ($this->intPositions[$y] ?? []) +
                            array_fill_keys(range($match[1], $match[1] + (strlen($match[0]) - 1)), $intOffset);

                        $intOffset++;
                    } else {
                        $this->symbolPositions[] = [
                            'symbol' => $match[0],
                            'y'      => $y,
                            'x'      => $match[1],
                        ];
                    }
                }
            }
        }

        $ints = $this->ints;

        // List out every part number (every int that
        // is adjacent to a symbol)
        // -1/-1  0/-1  1/1
        // -1/ 0   $    1/0
        //  1/-1  1/ 0  1/1
        foreach ($this->symbolPositions as $symbolPosition) {
            $key = $symbolPosition['symbol'] . $symbolPosition['x'] . '/' . $symbolPosition['y'];

            foreach (range(-1, 1) as $xModifier) {
                foreach (range(-1, 1) as $yModifier) {
                    // Symbol position, ignoring
                    if (0 === $xModifier && 0 === $yModifier) {
                        continue;
                    }

                    $intOffset = $this->findIntOffset(
                        $symbolPosition['y'] + $yModifier,
                        $symbolPosition['x'] + $xModifier
                    );

                    if (false !== $intOffset && array_key_exists($intOffset, $ints)) {
                        $this->partNumbers[$key] = array_merge(
                            $this->partNumbers[$key] ?? [],
                            [$ints[$intOffset]]
                        );

                        unset($ints[$intOffset]);
                    }
                }
            }
        }
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): mixed
    {
        $sum = 0;

        array_walk($this->partNumbers, function ($array) use (&$sum) {
            $sum += array_sum($array);
        });

        return $sum;
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): mixed
    {
        $ratios = [];

        array_walk($this->partNumbers, function ($array, $position) use (&$ratios) {
            if (2 === count($array) && str_contains($position, '*')) {
                $ratios[] = array_product($array);
            }
        });

        return array_sum($ratios);
    }

    ////////////////
    // METHODS
    ////////////////

    /**
     * Finds out if we have an int offset
     * for the position X/Y
     *
     * @param int $y
     * @param int $x
     *
     * @return int|false
     */
    private function findIntOffset(int $y, int $x): int|false
    {
        return $this->intPositions[$y][$x] ?? false;
    }
}
