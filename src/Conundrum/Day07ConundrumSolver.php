<?php

declare(strict_types=1);

namespace App\Conundrum;

use App\Entity\Day07\Hand;
use App\Entity\Day07\Type;

// /// Day 7: Camel Cards ///
class Day07ConundrumSolver extends AbstractConundrumSolver
{
    private const TYPES = [
        [
            'name'           => 'high card',
            'differentCards' => 5,
            'maxRepetitions' => 1,
        ],
        [
            'name'           => 'one pair',
            'differentCards' => 4,
            'maxRepetitions' => 2,
        ],
        [
            'name'           => 'two pair',
            'differentCards' => 3,
            'maxRepetitions' => 2,
        ],
        [
            'name'           => 'three of a kind',
            'differentCards' => 3,
            'maxRepetitions' => 3,
        ],
        [
            'name'           => 'full house',
            'differentCards' => 2,
            'maxRepetitions' => 3,
        ],
        [
            'name'           => 'four of a kind',
            'differentCards' => 2,
            'maxRepetitions' => 4,
        ],
        [
            'name'           => 'five of a kind',
            'differentCards' => 1,
            'maxRepetitions' => 5,
        ],
    ];
    private const JOKER = 'J';
    private const CARDS = [
        'A'         => 14,
        'K'         => 13,
        'Q'         => 12,
        self::JOKER => 11,
        'T'         => 10,
    ];

    private array $types = [];
    private int $i = 0;

    public function __construct(string $folder)
    {
        parent::__construct($folder);
    }

    public function prepare(): void
    {
        foreach (self::TYPES as $key => $type) {
            $this->types[] = new Type(
                $type['name'],
                $key + 1,
                $type['differentCards'],
                $type['maxRepetitions']
            );
        }
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

        // Associate each hand to its type
        foreach ($this->getInput() as $line) {
            [$hand, $bid] = explode(' ', $line);
            $hand = new Hand($hand, (int) $bid);

            $this->associateType($hand, $part);

            $byTypes[$hand->type->weight][] = $hand;
        }

        // Reorder from most to less powerful type
        krsort($byTypes);

        foreach ($byTypes as &$hands) {
            // Reorder hands with same type from most to less powerful by comparing their cards
            usort($hands, function (Hand $a, Hand $b) use ($part) {
                for ($j = 0; $j < 5; $j++) {
                    if ($a->cards[$j] === $b->cards[$j]) {
                        continue;
                    }

                    return $this->compareCardsStrength($a->cards[$j], $b->cards[$j], $part);
                }

                return 0;
            });

            // Combine hands with their rank, going from most to less valuable and update next rank
            $ranksInType = range($this->i, $this->i - count($hands) + 1);
            $hands = array_combine($ranksInType, array_values($hands));
            $orderedHands += $hands;
            $this->i -= count($hands);
        }

        $winnings = 0;

        array_walk($orderedHands, function (Hand $hand, int $rank) use (&$winnings) {
            $winnings += $hand->bid * $rank;
        });

        return $winnings;
    }

    private function associateType(Hand $hand, int $part = self::PART_ONE): void
    {
        // Group cards by number of repetitions
        $cardsInHand = array_reverse(array_count_values(str_split($hand->cards)), true);
        $jokers = $cardsInHand[self::JOKER] ?? 0;

        // Transform jokers into the most useful card
        if (self::PART_TWO === $part && 0 < $jokers) {
            unset($cardsInHand[self::JOKER]);

            // Hand with only joker cards
            if (empty($cardsInHand)) {
                $cardsInHand[self::JOKER] = 0;
            }

            $maxKey = array_search(max($cardsInHand), $cardsInHand);
            $cardsInHand[$maxKey] += $jokers;
        }

        $hand->groupedCards = $cardsInHand;

        /** @var Type $type */
        foreach ($this->types as $type) {
            if ($type->isTypeOfHand($hand)) {
                $hand->type = $type;

                return;
            }
        }
    }

    private function compareCardsStrength(string $cardA, string $cardB, int $part): int
    {
        return $this->getCardStrength($cardA, $part) < $this->getCardStrength($cardB, $part) ? 1 : -1;
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
