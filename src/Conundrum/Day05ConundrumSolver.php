<?php

declare(strict_types=1);

namespace App\Conundrum;

// /// Day 5: If You Give A Seed A Fertilizer ///
use App\Entity\Day05\Map;
use App\Entity\Day05\Range;

class Day05ConundrumSolver extends AbstractConundrumSolver
{
    private const SEED = 'seed';
    private const LOCATION = 'location';

    private array $maps = [];
    private array $availableSeeds = [];
    private ?int $minLocation = null;

    public function __construct(string $folder)
    {
        parent::__construct($folder, PHP_EOL . PHP_EOL);
    }

    public function prepare(): void
    {
        $input = $this->getInput();

        preg_match_all('/\d+/', array_shift($input), $matches, PREG_PATTERN_ORDER);

        $this->availableSeeds = $matches[0];
        $this->maps = $input;
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): mixed
    {
        foreach ($this->availableSeeds as $seed) {
            $source = $seed;

            foreach ($this->maps as $map) {
                $map = explode(PHP_EOL, $map);
                array_shift($map);

                foreach (array_filter($map) as $line) {
                    [$destinationRangeStart, $sourceRangeStart, $length] = explode(' ', $line);
                    $sourceRangeEnd = $sourceRangeStart + $length - 1;

                    if ($sourceRangeStart > $source || $sourceRangeEnd < $source) {
                        $destination = $source;

                        continue;
                    }

                    $destination = $source === $sourceRangeStart ?
                        $destinationRangeStart :
                        $destinationRangeStart + ($source - $sourceRangeStart);

                    break;
                }

                $source = $destination ?? $source;
            }

            $this->updateMinLocation($source);
        }

        return $this->minLocation;
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): mixed
    {
        $start = $this->availableSeeds[0];
        $rangesOfSeeds = $maps = [];

        // Initialize seed ranges
        foreach ($this->availableSeeds as $key => $seed) {
            // Impair
            if (1 === ($key + 1) % 2) {
                $start = $seed;

                continue;
            }

            $length = $seed;
            $rangesOfSeeds[] = new Range((int) $start, (int) ($start + $length - 1));
        }

        // Initialize maps
        foreach ($this->maps as $map) {
            $map = explode(PHP_EOL, $map);
            $object = (new Map())->setSourceAndDestination(array_shift($map));

            foreach (array_filter($map) as $line) {
                $object->computeLine($line);
            }

            $maps[] = $object;
        }

        $locations = $this->computeLocationRanges($maps, $rangesOfSeeds);

        if (empty($locations)) {
            return self::UNDETERMINED;
        }

        return min(array_map(static fn(Range $range) => $range->from, $locations));
    }

    ////////////////
    // METHODS
    ////////////////

    private function updateMinLocation(int $location): void
    {
        if (null === $this->minLocation || $this->minLocation > $location) {
            $this->minLocation = $location;
        }
    }

    private function computeLocationRanges(array $maps, array $rangesOfSeeds): array
    {
        // For every map, converts every range from source
        // into one or more destination range
        /** @var Map $map */
        foreach ($maps as $map) {
            $rangesByMaps[$map->destination] = [];
            $ranges = self::SEED === $map->source ? $rangesOfSeeds : $rangesByMaps[$map->source];

            foreach ($ranges as $rangeList) {
                $rangesByMaps[$map->destination] = array_merge(
                    $rangesByMaps[$map->destination],
                    $map->convert($rangeList)
                );
            }
        }

        return $rangesByMaps[self::LOCATION] ?? [];
    }
}
