<?php

namespace App\Entity\Day11;

use App\Entity\AbstractPosition;

class Position extends AbstractPosition
{
    public function __construct(public int $row, public int $column)
    {
        parent::__construct($row, $column);
    }
}
