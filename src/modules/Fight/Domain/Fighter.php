<?php

namespace modules\Fight\Domain;

use modules\Fight\Application\Exceptions\InvalidFighterException;

class Fighter
{
    private float $health;
    private float $strength;
    private float $defence;
    private float $speed;
    private float $luck;
    private SkillCollection $skills;
    private int $fighterId;
    private string $battleStatus;

    /**
     * @throws InvalidFighterException
     */
    public function __construct(float $health, float $strength, float $defence, float $speed, float $luck, SkillCollection $skills, $fighterId = 0)
    {
        if ($health <= 0) {
            throw new InvalidFighterException('Fighter health should be a positive integer');
        }
        if ($strength <= 0) {
            throw new InvalidFighterException('Fighter strength should be a positive integer');
        }

        $this->health = $health;
        $this->strength = $strength;
        $this->defence = $defence;
        $this->speed = $speed;
        $this->luck = $luck;
        if ($skills->hasObjects() > 0) {
            $types = [];
            /** @var Skill $skill */
            foreach ($skills as $skill) {
                $types[] = $skill->getType();
            }
            if (count($types) != $skills->count()) {
                throw new InvalidFighterException('A fighter can have only one skill per type');
            }
        }
        $this->skills = $skills;
        $this->fighterId = $fighterId;
        $this->battleStatus = OutcomeStatus::OUTCOME_UNDECIDED;
    }

    public function getFighterId()
    {
        return $this->fighterId;
    }

    /**
     * @throws InvalidFighterException
     */
    public function advanceFighterBattleStatus(string $status)
    {
        if (false === in_array($status, OutcomeStatus::OUTCOME_STATUS) || $status == OutcomeStatus::OUTCOME_UNDECIDED) {
            throw new InvalidFighterException('Fighter can only advance to winner or looser status');
        }

        $this->battleStatus = $status;
    }

    public function getBattleStatus(): string
    {
        return $this->battleStatus;
    }

    /**
     * @throws InvalidFighterException
     */
    public function setFighterId($fighterId): void
    {
        if (!$fighterId) {
            throw new InvalidFighterException('Invalid fighter id');
        }
        $this->fighterId = $fighterId;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    public function getStrength(): int
    {
        return $this->strength;
    }

    public function getDefence(): int
    {
        return $this->defence;
    }

    public function getSpeed(): int
    {
        return $this->speed;
    }

    public function getLuck(): int
    {
        return $this->luck;
    }

    public function getSkills(): SkillCollection
    {
        return $this->skills;
    }

    public function toArray(): array
    {
        return [
            'health' => $this->health,
            'strength' => $this->strength,
            'defence' => $this->defence,
            'speed' => $this->speed,
            'luck' => $this->luck,
            'skills' => $this->skills->toArray(),
            'fighterId' => $this->fighterId
        ];
    }

    public function isNext()
    {
        $isNext = false;
        if (!empty($this->skills)) {
            /** @var Skill $skill */
            foreach ($this->skills as $skill) {
                if ($skill->getType() == SkillTypes::TURN_TYPE) {
                    $isNext = $skill->applyBonus($isNext);
                }
            }
        }

        return $isNext;
    }

    public function takeDamage(int $attack)
    {
        $attack = $this->defend($attack);

        $this->health -= $attack;
        echo "Fighter id " . $this->fighterId . " took " . $attack . " damage. Remaining health: " . $this->health . "\n";
    }

    private function defend(int $attack)
    {
        if (!empty($this->skills)) {
            /** @var Skill $skill */
            foreach ($this->skills as $skill) {
                if ($skill->getType() == SkillTypes::DEFENCE_TYPE) {
                    $attack = $skill->applyBonus($attack);
                }
            }
        }

        if ($attack <= $this->defence) {
            $attack = 0;
        } else {
            $attack -= $this->defence;
        }

        return $attack;
    }
}