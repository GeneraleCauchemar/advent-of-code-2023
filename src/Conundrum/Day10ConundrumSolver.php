<?php

declare(strict_types=1);

namespace App\Conundrum;

// /// Day Day 10: Pipe Maze ///
use App\Entity\Day10\DomainLogic;
use App\Entity\Day10\Position;
use App\Entity\Day10\TerrainCost;
use JMGQ\AStar\AStar;

class Day10ConundrumSolver extends AbstractConundrumSolver
{
    private Position $start;
    private array $possibleLoops = [];

    public function __construct(string $folder)
    {
        parent::__construct($folder);
    }

    public function prepare(): void
    {

    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): mixed
    {
        $positions = [];

        foreach ($this->getInput() as $keyY => $line) {
            $y = [];

            foreach (str_split($line) as $keyX => $pipe) {
                $position = new Position($keyY, $keyX, $pipe);

                if ('S' === $pipe) {
                    $this->start = $position;
                }

                $y[$keyX] = $position;
            }

            $positions[$keyY] = $y;
        }

        $terrainCost = new TerrainCost($positions);
        $domainLogic = new DomainLogic($terrainCost);
        $aStar = new AStar($domainLogic);

        $this->findPossibleLoops($positions);

        $loopLength = 0;

        foreach ($this->possibleLoops as [$start, $end]) {
            $solution = $aStar->run($start, $end);
            $loopLength = count($solution) + 1;
        }

        return $loopLength / 2;
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): mixed
    {
        return self::UNDETERMINED;
    }

    ////////////////
    // METHODS
    ////////////////

    private function findPossibleLoops(array $positions): void
    {
        $starts = [];

        foreach ($positions as $line) {
            foreach ($line as $position) {
                if ($this->start->isAdjacentTo($position) && $position->isOrientedTowards($this->start)) {
                    $starts[] = $position;
                }
            }
        }

        foreach ($starts as $start) {
            foreach ($starts as $position) {
                // Ignore loops that'll already be computed in the other way
                if ($start === $position || in_array([$position, $start], $this->possibleLoops)) {
                    continue;
                }

                $this->possibleLoops[] = [$start, $position];
            }
        }
    }
}
