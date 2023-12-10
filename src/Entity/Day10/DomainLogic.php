<?php

namespace App\Entity\Day10;

use JMGQ\AStar\DomainLogicInterface;

class DomainLogic implements DomainLogicInterface
{
    private TerrainCost $terrainCost;
    private array $positions;

    public function __construct(TerrainCost $terrainCost)
    {
        $this->terrainCost = $terrainCost;
        $this->positions = $terrainCost->positions;
    }

    public function getAdjacentNodes(mixed $node): iterable
    {
        $adjacentNodes = [];

        [$startingRow, $endingRow, $startingColumn, $endingColumn] = $this->calculateAdjacentBoundaries($node);

        for ($row = $startingRow; $row <= $endingRow; $row++) {
            for ($column = $startingColumn; $column <= $endingColumn; $column++) {
                $adjacentNode = $this->positions[$row][$column];

                if (!$node->isEqualTo($adjacentNode)) {
                    $adjacentNodes[] = $adjacentNode;
                }
            }
        }

        return $adjacentNodes;
    }

    public function calculateRealCost(mixed $node, mixed $adjacent): float | int
    {
        if ($node->isAdjacentTo($adjacent)) {
            return $this->terrainCost->getCost($node, $adjacent);
        }

        return TerrainCost::INFINITE;
    }

    public function calculateEstimatedCost(mixed $fromNode, mixed $toNode): float | int
    {
        if ($fromNode === $toNode) {
            return PHP_INT_MAX;
        }

        return $this->euclideanDistance($fromNode, $toNode);
    }

    private function euclideanDistance(Position $a, Position $b): float
    {
        $rowFactor = ($a->row - $b->row) ** 2;
        $columnFactor = ($a->column - $b->column) ** 2;

        return sqrt($rowFactor + $columnFactor);
    }

    private function calculateAdjacentBoundaries(Position $position): array
    {
        if ($position->row === 0) {
            $startingRow = 0;
        } else {
            $startingRow = $position->row - 1;
        }

        if ($position->row === $this->terrainCost->getTotalRows() - 1) {
            $endingRow = $position->row;
        } else {
            $endingRow = $position->row + 1;
        }

        if ($position->column === 0) {
            $startingColumn = 0;
        } else {
            $startingColumn = $position->column - 1;
        }

        if ($position->column === $this->terrainCost->getTotalColumns() - 1) {
            $endingColumn = $position->column;
        } else {
            $endingColumn = $position->column + 1;
        }

        return [$startingRow, $endingRow, $startingColumn, $endingColumn];
    }
}
