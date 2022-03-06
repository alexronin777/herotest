<?php

namespace modules\Fight\Port\Adapter\Controller;

use modules\Fight\Application\Service\FightService;
use modules\Fight\Domain\FightEvaluationDto;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EvaluateFightCommand extends Command
{
    const INPUT_FIGHT_ID = 'fightId';
    const INPUT_BATCH_LIMIT = 'limit';
    const BATCH_DEFAULT = 1;

    private FightService $service;

    public function __construct(FightService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    protected function configure()
    {
        $this
            ->setName('emagia')
            ->setDescription('Evaluate fights from Emagia realm')
            ->addOption(
                self::INPUT_FIGHT_ID,
                null,
                InputOption::VALUE_OPTIONAL,
                "Battle identifier. Ex: --fightId"
            )
            ->addOption(
                self::INPUT_BATCH_LIMIT,
                null,
                InputOption::VALUE_OPTIONAL,
                "Number of battle evaluations to evaluate in one cycle",
                self::BATCH_DEFAULT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = false;

        $data = $input->getOptions();
        if (!empty($data[self::INPUT_FIGHT_ID]) && intval($data[self::INPUT_FIGHT_ID]) <= 0) {
            throw new \InvalidArgumentException('Fight identifier needs to be a positive integer.');
        }

        try {
            $cmd = new FightEvaluationDto($data[self::INPUT_BATCH_LIMIT], $data[self::INPUT_FIGHT_ID]);
            $result = $this->service->evaluateFights($cmd);
        } catch (\Throwable $e) {
            print_r($e->getMessage());
        }

        return intval($result);
    }
}