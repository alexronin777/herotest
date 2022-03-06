<?php

namespace modules\Fight\Port\Adapter\Service;

use modules\Fight\Application\Exceptions\FightException;
use modules\Fight\Application\Exceptions\InvalidFighterException;
use modules\Fight\Application\Exceptions\RepositoryException;
use modules\Fight\Application\Service\FighterServiceInterface;
use modules\Fight\Domain\Fight;
use modules\Fight\Domain\Fighter;
use modules\Fight\Domain\FightEvaluationDto;
use modules\Fight\Domain\FightFilter;
use modules\Fight\Domain\FightRepositoryInterface;
use modules\Fight\Domain\FightStatus;
use modules\Fight\Port\Adapter\Helper\FightMapper;

class FighterService implements FighterServiceInterface
{
    private FightRepositoryInterface $repo;
    private FightMapper $mapper;

    public function __construct(FightRepositoryInterface $repo, FightMapper $mapper)
    {
        $this->repo = $repo;
        $this->mapper = $mapper;
    }

    /**
     * @throws FightException
     */
    public function storeFightResult(Fight $fight)
    {
        if ($fight->getFightId() > 0 && count($fight->getOpponents()) > 0) {
            foreach ($fight->getOpponents() as $opponent) {
                $this->repo->updateFightOpponents($fight->getFightId(), $opponent);
            }
            $this->repo->updateFightHistoryStatus($fight->getFightId(), $fight->getStatus());
        } else {
            throw new FightException('Can not save a fight result without a fight id or opponents.');
        }

    }

    /**
     * @throws InvalidFighterException
     * @throws FightException
     */
    public function pickFights(FightEvaluationDto $cmd): array
    {
        $filter = $this->getFightsFilter($cmd);
        $rawFights = $this->repo->pickFights($filter);

        $fights = [];
        foreach ($rawFights as $raw) {
            $fighters = [];
            foreach ($raw['opponents'] as $opponent) {
                $fighters[] = $this->mapper->toFighter($opponent, $opponent['Skills']);
            }
            $fight = $this->mapper->toFight($fighters, $raw['fightDetails']['CreatedAt']);
            $fight->setFightId($raw['fightDetails']['FightsHistoryId']);
            $fights[] = $fight;
        }
print_r($fights);
        return $fights;
    }

    public function enqueueFight(Fight $fight): int
    {
        return $this->repo->enqueueFight($fight);
    }

    /**
     * @throws \Exception
     */
    public function saveFighter(Fighter $fighter): int
    {
        try {
            return $this->repo->saveFighter($fighter);
        } catch (RepositoryException $e) {
            throw new \Exception($e->getMessage() . 'with: ' . json_encode($fighter->toArray()));
        }
    }

    /**
     * @return Fighter[]
     * @throws InvalidFighterException
     */
    public function getOpponents(array $fightersDetails): array
    {
        $opponents = [];
        foreach ($fightersDetails as $fighterDetail) {
            $skills = [];
            if (!empty($fighterDetail['skills'])) {
                $skills = $this->repo->getSkills($fighterDetail['skills']);
            }

            $opponents[] = $this->mapper->toFighter($fighterDetail, $skills);
        }

        return $opponents;
    }

    private function getFightsFilter(FightEvaluationDto $cmd): FightFilter
    {
        return new FightFilter($cmd->getLimit(), FightStatus::FIGHT_STATUS_PENDING, intval($cmd->getFightId()));
    }
}