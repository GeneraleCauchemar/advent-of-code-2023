<?php

declare(strict_types=1);

namespace App\Conundrum;

// /// Day 1: Trebuchet?! ///
class Day01ConundrumSolver extends AbstractConundrumSolver
{
    private const PLAIN_TEXT_DIGITS = [
        'one',
        'two',
        'three',
        'four',
        'five',
        'six',
        'seven',
        'eight',
        'nine',
    ];

    private int $sum;

    public function __construct(string $folder)
    {
        parent::__construct($folder);
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): mixed
    {
        $this->sum = 0;

        foreach ($this->getInput() as $line) {
            preg_match_all('/\d/', $line, $digits);

            $this->updateSum($digits[0]);
        }

        return $this->sum;
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): mixed
    {
        $this->sum = 0;

        foreach ($this->getInput(self::PART_TWO) as $line) {
            preg_match_all('/\d/', $line, $matches, PREG_OFFSET_CAPTURE);

            // Reset
            $matches = $matches[0];
            $digits = [];

            foreach (array_merge($matches, $this->findPlainTextDigits($line)) as $item) {
                if (1 < count($item)) {
                    $digits[$item[1]] = $item[0];
                }
            }

            // Reorder by offset then reset keys
            ksort($digits);
            $this->updateSum(array_values($digits));
        }

        return $this->sum;
    }

    ////////////////
    // METHODS
    ////////////////

    private function findPlainTextDigits(string $input): array
    {
        $digits = [];

        foreach (self::PLAIN_TEXT_DIGITS as $plainTextDigit) {
            $lastOffset = 0;

            while (($lastOffset = strpos($input, $plainTextDigit, $lastOffset)) !== false) {
                $digits[] = [$plainTextDigit, $lastOffset];
                $lastOffset = $lastOffset + strlen($plainTextDigit);
            }
        }

        return $digits;
    }

    private function updateSum(array $digits): void
    {
        if (0 < count($digits)) {
            $this->sum += (int) ($this->resolveDigit($digits[0]) . $this->resolveDigit(end($digits)));
        }
    }

    private function resolveDigit(string $digit): string
    {
        $offset = array_search($digit, self::PLAIN_TEXT_DIGITS);

        return false !== $offset ? (string) ($offset + 1) : $digit;
    }
}
