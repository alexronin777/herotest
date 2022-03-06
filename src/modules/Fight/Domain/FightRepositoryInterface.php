<?php

namespace modules\Fight\Domain;

use modules\Fight\Application\Exceptions\RepositoryException;

interface FightRepositoryInterface
{
    public function getSkills(array $skillsIds);

    /**
     * @throws RepositoryException
     */
    public function saveFighter(Fighter $fighter): int;

    public function enqueueFight(Fight $fight): int;

    public function pickFights(FightFilter $filter);

    public function updateFightOpponents(int $fightId, Fighter $opponent);

    public function updateFightHistoryStatus(int $fightId, string $status);
}