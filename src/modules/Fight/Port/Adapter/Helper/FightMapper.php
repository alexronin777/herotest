<?php

namespace modules\Fight\Port\Adapter\Helper;

use modules\Fight\Application\Exceptions\FightException;
use modules\Fight\Application\Exceptions\InvalidFighterException;
use modules\Fight\Domain\Fight;
use modules\Fight\Domain\Fighter;
use modules\Fight\Domain\Skill;
use modules\Fight\Domain\SkillCollection;

class FightMapper
{
    /**
     * @throws InvalidFighterException
     */
    public function toFighter(array $fightDetails, array $skills): Fighter
    {
        $skills = $this->toSkillCollection($skills);

        return new Fighter(
            intval($fightDetails['Health']),
            intval($fightDetails['Strength']),
            intval($fightDetails['Defence']),
            intval($fightDetails['Speed']),
            intval($fightDetails['Luck']),
            $skills,
            ($fightDetails['FighterId'] ?? 0)
        );
    }

    /**
     * @throws InvalidFighterException
     */
    public function toSkillCollection(array $details): SkillCollection
    {
        $skills = new SkillCollection();

        if (!empty($details)) {
            foreach ($details as $detail) {
                $skills->add(new Skill($detail['SkillId'], $detail['Type'], $detail['Name'], $detail['Description'], $detail['Probability'], $detail['Operator'], $detail['Value']));
            }
        }

        return $skills;
    }

    /**
     * @throws FightException
     * @throws \Exception
     */
    public function toFight(array $opponents, string $date): Fight
    {
        return new Fight($opponents, new \DateTimeImmutable($date));
    }
}