<?php

declare(strict_types=1);

namespace App\Command;

use App\Conundrum\AbstractConundrumSolver;
use App\Exception\SolverNotFoundException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:resolve-conundrums')]
class ResolveConundrumsCommand extends Command
{
    private string $day;
    private bool $testMode;

    protected function configure(): void
    {
        $this
            ->addArgument('day', InputArgument::REQUIRED, '')
            ->addOption('with-test-input', 'T')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->day = $input->getArgument('day');
        $this->testMode = $input->getOption('with-test-input');

        $io = new SymfonyStyle($input, $output);

        try {
            // Solve
            /** @var AbstractConundrumSolver $conundrumSolver */
            $conundrumSolver = $this->getSolverForDay();
            $result = $conundrumSolver->execute($this->testMode);

            // Display results
            $result = $this->formatResultForDisplay($result);
            $banner = sprintf(
                '<christmas_white>%s</>',
                str_repeat(' ', Helper::width(Helper::removeDecoration($io->getFormatter(), $result)))
            );

            $io->text([
                sprintf(
                    '<christmas_red%s>%s</>',
                    $this->testMode ? '_test' : '',
                    str_pad(
                        strtoupper(
                            sprintf(
                                '%s December %s, 2023 ',
                                $this->testMode ? ' // TEST //' : '',
                                $this->day,
                            )
                        ),
                        Helper::width(Helper::removeDecoration($io->getFormatter(), $result)),
                        ' '
                    )
                ),
                $banner,
                $result,
                $banner,
                '',
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $banner = sprintf(
                '<error>%s</>',
                str_repeat(' ', Helper::width(Helper::removeDecoration($io->getFormatter(), $error)))
            );

            $io->text([
                $banner,
                $error,
                $banner,
                '',
            ]);
        }

        return Command::FAILURE;
    }

    /**
     * @throws SolverNotFoundException
     */
    private function getSolverForDay()
    {
        $day = $this->getDay($this->day);
        $className = 'App\\Conundrum\\Day' . $day . 'ConundrumSolver';

        return class_exists($className) ?
            new $className($day) :
            throw new SolverNotFoundException(
                sprintf('<error>There is no solver available for day %s!</error>', $this->day)
            );
    }

    private function getDay(string $day): string
    {
        return 1 === strlen($day) ? '0' . $day : $day;
    }

    private function formatResultForDisplay(array $result): string
    {
        $line = explode('|', ' Solution | to | part | one | is | %s | and | solution | to | part | two | is | %s |.');

        array_walk($line, function (&$word, $key) {
            $color = match (true) {
                str_contains($word, '%s') => 'green' . ($this->testMode ? '_test' : ''),
                !($key & 1) => 'red' . ($this->testMode ? '_test' : ''),
                default => 'white',
            };

            $word = sprintf('<christmas_%s>%s</>', $color, $word);
        });

        return '<christmas_white> 🎄 </>' . sprintf(implode('', $line), ...$result) . '<christmas_white> 🎄 </>';
    }
}
