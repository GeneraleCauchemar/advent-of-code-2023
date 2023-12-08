<?php

namespace App\Entity\Day05;

class Map
{
    public string $source;
    public string $destination;
    public array $limits;
    public array $conversion = [];

    public function setSourceAndDestination(string $name): self
    {
        preg_match('/(?<source>[a-z]+)-to-(?<destination>[a-z]+) map:/', $name, $matches);

        $this->source = $matches['source'];
        $this->destination = $matches['destination'];

        return $this;
    }

    public function computeLine(string $line): void
    {
        [$destinationRangeStart, $sourceRangeStart, $length] = array_map('intval', explode(' ', $line));
        $nextRangeStart = $sourceRangeStart + $length;
        $operand = $destinationRangeStart - $sourceRangeStart;

        // Add lower limit of range and the conversion operand to the lists
        $this->updateLimits($sourceRangeStart);
        $this->updateConversions(true, $sourceRangeStart, $operand);

        // And do the same for the external upper limit if it isn't already in both lists
        $this->updateLimits($nextRangeStart);
        $this->updateConversions(false, $nextRangeStart, 0);

        sort($this->limits);
    }

    public function convert(Range $range): array
    {
        $ranges = [];
        $lowerBound = $range->from;
        $upperBound = $range->to;

        foreach ($this->limits as $i => $limit) {
            // Out of bounds: every remaining value isn't
            // defined between our given limits so source = destination
            if (array_key_last($this->limits) === $i) {
                $ranges[] = new Range($lowerBound, $upperBound);

                break;
            }

            $nextLowerBound = $this->limits[$i + 1];

            // If our lower bound is out of this range continue to next one
            if ($nextLowerBound <= $lowerBound) {
                continue;
            }

            // Get operand for whichever is the lowest between the limit and the lower bound
            // and initialize new range
            $operand = $this->conversion[min($lowerBound, $limit)] ?? 0;
            $ranges[] = new Range($lowerBound, min($upperBound, $nextLowerBound - 1), $operand);

            if ($upperBound < $nextLowerBound) {
                break;
            }

            $lowerBound = $nextLowerBound;
        }

        // Convert all to destination using defined operands
        return array_map(static fn(Range $range) => $range->convertToDestinationRange(), $ranges);
    }

    private function updateLimits(int $value): void
    {
        // If we don't know the conversion operand for this value, it's a new limit
        if (!isset($this->conversion[$value])) {
            $this->limits[] = $value;
        }
    }

    private function updateConversions(bool $forceUpdate, int $key, int $operand): void
    {
        // Do not override the conversion operand for this key unless specified
        if ($forceUpdate || !isset($this->conversion[$key])) {
            $this->conversion[$key] = $operand;
        }
    }
}
