<?php

declare(strict_types=1);

namespace App\Conundrum;

// /// Day 7: Camel Cards ///
class Day07ConundrumSolver extends AbstractConundrumSolver
{
    private const JOKER = 'J';
    private const CARDS = [
        'A'         => 14,
        'K'         => 13,
        'Q'         => 12,
        self::JOKER => 11,
        'T'         => 10,
    ];
    private int $i = 0;

    public function __construct(string $folder)
    {
        parent::__construct($folder);
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): mixed
    {
        return $this->computeWinnings();
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): mixed
    {
        return $this->computeWinnings(self::PART_TWO);
    }

    ////////////////
    // METHODS
    ////////////////

    private function computeWinnings(int $part = self::PART_ONE): int
    {
        $this->resetI();

        $byTypes = [];
        $orderedHands = [];

        // For each hand, compute its type
        foreach ($this->getInput() as $line) {
            [$hand, $bid] = explode(' ', $line);
            $type = $this->computeType($hand, $part);
            $byTypes[$type][] = [$hand, $bid];
        }

        // Reorder from most to less powerful type
        krsort($byTypes);

        foreach ($byTypes as &$hands) {
            // Reorder hands with same type from more to less powerfull
            usort($hands, function (array $a, array $b) use ($part) {
                // La fonction de comparaison doit retourner un entier inférieur à, égal à, ou supérieur à 0 si le premier
                // argument est considéré comme, respectivement, inférieur à, égal à, ou supérieur au second.
                for ($j = 0; $j < 5; $j++) {
                    if ($a[0][$j] === $b[0][$j]) {
                        continue;
                    }

                    return $this->getCardStrength($a[0][$j], $part) < $this->getCardStrength($b[0][$j], $part) ? 1 : -1;
                }

                return 0;
            });

            // Combine hands with their rank, going from most to less valuable
            $hands = array_combine(
                range($this->i, $this->i - count($hands) + 1),
                array_values($hands)
            );
            $orderedHands += $hands;
            $this->i -= count($hands);
        }

        $winnings = 0;

        array_walk($orderedHands, function ($value, $key) use (&$winnings) {
            $winnings += $value[1] * $key;
        });

        return $winnings;
    }

    private function computeType(string $hand, int $part = self::PART_ONE): int
    {
        // Group cards by number of repetitions
        $hand = array_reverse(array_count_values(str_split($hand)), true);
        $jokers = $hand[self::JOKER] ?? 0;
        $differentCards = count($hand);

        // 5
        if (1 === $differentCards) {
            return 7;
        }

        // 4/1 (6) ou 3/2 (5)
        if (2 === $differentCards) {
            if (self::PART_TWO === $part && array_key_exists(self::JOKER, $hand)) {
                // > 5
                return 7;
            }

            return 4 === max($hand) ? 6 : 5;
        }

        // 3/1/1 (4) ou 2/2/1 (3)
        if (3 === $differentCards) {
            // 1, 2 or 3 jokers available
            if (self::PART_TWO === $part && 0 < $jokers) {
                /**
                 * si on a 1 joker, 1+3 > 4/1 OU 1+2 > 3/2
                 * si on a 3 jokers, 3 + 1 > 4/1
                 * si on a 2 jokers, 2 + 2 > 4/1
                 */

                return 1 !== $jokers || 3 === max($hand) ? 6 : 5;
            }

            return 3 === max($hand) ? 4 : 3;
        }

        // 2/1/1/1 (2)
        if (4 === $differentCards) {
            // > 3/1/1 or 2/1/1/1
            return self::PART_TWO === $part && 0 < $jokers ? 4 : 2;
        }

        // > 2/1/1/1 or 1/1/1/1/1
        return self::PART_TWO === $part && 1 === $jokers ? 2 : 1;
    }

    private function getCardStrength(string $card, int $part = self::PART_ONE): int
    {
        // Strength of joker card is lessened in part two
        return self::JOKER === $card && self::PART_TWO === $part ? 1 : self::CARDS[$card] ?? (int) $card;
    }

    private function resetI(): void
    {
        $this->i = count($this->getInput());
    }
}
