<?php

namespace App\Entity;

use JMGQ\AStar\Node\NodeIdentifierInterface;

abstract class AbstractPosition implements NodeIdentifierInterface
{
    public function __construct(public int $row, public int $column)
    {
    }

    public function getUniqueNodeId(): string
    {
        return ((string) $this->row) . 'x' . ((string) $this->column);
    }

    public function isEqualTo(AbstractPosition $position): bool
    {
        return $this->row === $position->row && $this->column === $position->column;
    }

    public function isAdjacentTo(AbstractPosition $position): bool
    {
        $rowDiff = $this->getRowDiff($position, true);
        $columnDiff = $this->getColumnDiff($position, true);

        return (1 === $rowDiff && 0 === $columnDiff) || (1 === $columnDiff && 0 === $rowDiff);
    }

    private function getRowDiff(AbstractPosition $position, bool $absolute = false): int
    {
        return $absolute ? abs($this->row - $position->row) : $this->row - $position->row;
    }

    private function getColumnDiff(AbstractPosition $position, bool $absolute = false): int
    {
        return $absolute ? abs($this->column - $position->column) : $this->column - $position->column;
    }

    public function __toString(): string
    {
        return $this->getUniqueNodeId();
    }
}
