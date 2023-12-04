<?php

declare(strict_types=1);

namespace App\Conundrum;

// /// Day 4: Scratchcards ///
class Day04ConundrumSolver extends AbstractConundrumSolver
{
    private int $points = 0;
    private array $cards = [];

    public function __construct(string $folder)
    {
        parent::__construct($folder);
    }

    public function prepare(): void
    {
        foreach ($this->getInput() as $card) {
            [$id, $winningNumbers, $numbersOnCard] = $this->getCardParameters($card);

            // Finds out how many winning numbers we have,
            // how many points the card is then worth and
            // which subsequent card copies they get us
            $wins = $this->wins($winningNumbers, $numbersOnCard);
            $this->points += $this->computePointsForCard($wins);
            $this->cards[$id] = [
                'copies' => 1,
                'wins'   => 0 < $wins ? range($id + 1, $id + $wins) : [],
            ];
        }
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): mixed
    {
        return $this->points;
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): mixed
    {
        foreach ($this->cards as $details) {
            $this->recursivelyIncrementCopiesOfCards($details['wins']);
        }

        return array_sum(array_column($this->cards, 'copies'));
    }

    ////////////////
    // METHODS
    ////////////////

    private function getCardParameters(string $card): array
    {
        preg_match('/Card\s+(?<id>\d+):\s+(?<winning>[\d ]+)\s+\|\s+(?<draw>[\d ]+)/', $card, $matches);

        return [
            (int) $matches['id'],
            array_filter(explode(' ', $matches['winning'])),
            array_filter(explode(' ', $matches['draw'])),
        ];
    }

    private function computePointsForCard(int $matching): int
    {
        $pointsForCard = 0;

        for ($i = 0; $i < $matching; $i++) {
            match ($i) {
                0 => $pointsForCard++,
                default => $pointsForCard *= 2
            };
        }

        return $pointsForCard;
    }

    private function wins(array $winningNumbers, array $numbersOnCard): int
    {
        return count(array_intersect($winningNumbers, $numbersOnCard));
    }

    private function recursivelyIncrementCopiesOfCards(array $ids): void
    {
        // For every ID, we get one more copy and then some
        // for every copy this card gets us
        foreach ($ids as $id) {
            $this->cards[$id]['copies']++;
            $this->recursivelyIncrementCopiesOfCards($this->cards[$id]['wins']);
        }
    }
}
