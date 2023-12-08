<?php

declare(strict_types=1);

namespace App\Conundrum;

use App\Exception\InputFileNotFoundException;

abstract class AbstractConundrumSolver implements ConundrumSolverInterface
{
    protected const PART_ONE = 1;
    protected const PART_TWO = 2;
    protected const UNDETERMINED = 'to be determined';

    private bool $testMode = false;
    private array|string $input;
    private array $testInputs;
    private string $day;
    private ?string $separator;
    private bool $keepAsString;

    public function __construct(
        string $day,
        ?string $separator = PHP_EOL,
        bool $keepAsString = false
    ) {
        $this->day = $day;
        $this->separator = $separator;
        $this->keepAsString = $keepAsString;
    }

    /**
     * @throws InputFileNotFoundException
     */
    public function execute(bool $testMode = false): array
    {
        $this->testMode = $testMode;

        $this->init();
        $this->prepare();

        return [
            $this->partOne(),
            $this->partTwo(),
        ];
    }

    public function prepare(): void
    {
    }

    public function partOne(): mixed
    {
        return self::UNDETERMINED;
    }

    public function partTwo(): mixed
    {
        return self::UNDETERMINED;
    }

    protected function isTestMode(): bool
    {
        return $this->testMode;
    }

    protected function getInput(int $part = self::PART_ONE): array|string
    {
        return $this->isTestMode() ? $this->getTestInput($part) : $this->input;
    }

    protected function getTestInput(int $part = self::PART_ONE)
    {
        if (array_key_exists($part, $this->testInputs)) {
            return $this->testInputs[$part];
        }

        return [];
    }

    /**
     * @throws InputFileNotFoundException
     */
    private function init(): void
    {
        $this->isTestMode() ? $this->initTestInputs() : $this->initInput();
    }

    private function initInput(): void
    {
        $path = sprintf('%s/../../Resources/input/%s.txt', __DIR__, $this->day);

        if (!file_exists($path)) {
            throw new InputFileNotFoundException(sprintf('<error>Missing input file at path "%s".</error>', $path));
        }

        $this->input = $this->getContent($path);
    }

    private function initTestInputs(): void
    {
        $this->testInputs = [];
        $partialPath = '%s/../../Resources/input/test/%s_%s.txt';
        $paths = [
            self::PART_ONE => sprintf($partialPath, __DIR__, $this->day, self::PART_ONE),
            self::PART_TWO => sprintf($partialPath, __DIR__, $this->day, self::PART_TWO),
        ];

        // If there is a different test input for each part
        foreach ($paths as $part => $path) {
            if (!file_exists($path)) {
                continue;
            }

            $this->testInputs[$part] = $this->getContent($path);
        }

        // If no separate test inputs were found, tries to find a common one
        if (empty($this->testInputs)) {
            $path = sprintf(str_replace('_%s', '', $partialPath), __DIR__, $this->day);

            if (file_exists($path)) {
                $this->testInputs = array_fill_keys([self::PART_ONE, self::PART_TWO], $this->getContent($path));
            }
        }
    }

    private function getContent(string $path): array|string
    {
        return $this->keepAsString ?
            trim(file_get_contents($path)) :
            array_filter(explode($this->separator, file_get_contents($path)));
    }
}
