<?php

namespace App\Entity\Day10;

class TerrainCost
{
    public const INFINITE = PHP_INT_MAX;

    public function __construct(public array $positions)
    {
    }

    public function getCost(Position $from, Position $to): int
    {
        if ($to->isGround || $to->isStartingPoint || !$to->isOrientedTowards($from)) {
            return self::INFINITE;
        }

        return 0;
    }

    public function getTotalRows(): int
    {
        return count($this->positions);
    }

    public function getTotalColumns(): int
    {
        return count($this->positions[0]);
    }
}
