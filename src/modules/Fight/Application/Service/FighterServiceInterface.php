<?php

namespace modules\Fight\Application\Service;

use modules\Fight\Application\Exceptions\FightException;
use modules\Fight\Application\Exceptions\InvalidFighterException;
use modules\Fight\Domain\Fight;
use modules\Fight\Domain\Fighter;
use modules\Fight\Domain\FightEvaluationDto;

interface FighterServiceInterface
{
    public function getOpponents(array $fightersDetails);

    public function saveFighter(Fighter $fighter): int;

    public function enqueueFight(Fight $fight);

    /**
     * @throws InvalidFighterException
     * @throws FightException
     */
    public function pickFights(FightEvaluationDto $cmd): array;

    /**
     * @throws FightException
     */
    public function storeFightResult(Fight $fight);
}