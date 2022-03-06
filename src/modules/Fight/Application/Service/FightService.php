<?php

namespace modules\Fight\Application\Service;

use modules\Fight\Application\Exceptions\FightException;
use modules\Fight\Application\Exceptions\InvalidFighterException;
use modules\Fight\Domain\Fight;
use modules\Fight\Domain\Fighter;
use modules\Fight\Domain\FightEvaluationDto;

class FightService
{
    private array $errors = [];
    private FighterServiceInterface $service;

    public function __construct(FighterServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @throws InvalidFighterException
     * @throws FightException
     */
    public function enqueueFight(array $opponents): int
    {
        $fighters = $this->service->getOpponents($opponents);

        /** @var Fighter $fighter */
        foreach ($fighters as $fighter) {
            $fighter->setFighterId($this->service->saveFighter($fighter));
        }

        return $this->service->enqueueFight(new Fight($fighters, new \DateTimeImmutable()));
    }

    public function evaluateFights(FightEvaluationDto $cmd): bool
    {
        try {
            $fights = $this->service->pickFights($cmd);
        } catch (FightException | InvalidFighterException $e) {
            $this->errors[] = $e->getMessage();
        }

        if (empty($fights)) {
            return true;
        }

        /** @var Fight $fight */
        foreach ($fights as $fight) {
            try {
                $fight->init();
            } catch (FightException | InvalidFighterException $e) {
                $this->errors[] = $e->getMessage();
                continue;
            }
            if ($fight->isFinished()) {
                try {
                    $this->service->storeFightResult($fight);
                } catch (FightException $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        }

        return (empty($this->errors));
    }
}