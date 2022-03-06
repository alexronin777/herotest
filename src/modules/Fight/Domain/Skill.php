<?php

namespace modules\Fight\Domain;

use modules\Fight\Application\Exceptions\InvalidFighterException;

class Skill
{
    private int $skillId;
    private string $type;
    private string $name;
    private string $description;
    private int $probability;
    private string $operator;
    private float $value;

    /**
     * @throws InvalidFighterException
     */
    public function __construct(int $skillId, string $type, string $name, string $description, int $probability, string $operator, float $value)
    {
        $this->skillId = $skillId;
        $type = strtoupper($type);
        if (false === in_array($type, SkillTypes::SKILL_TYPES)) {
            throw new InvalidFighterException('Only skill types allowed: ' . implode(",", SkillTypes::SKILL_TYPES));
        }
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->probability = $probability;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function getSkillId(): int
    {
        return $this->skillId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProbability(): int
    {
        return $this->probability;
    }

    public function applyBonus(int $statValue = 0)
    {
        $luck = $this->feelingLucky();
        if ($luck) {
            //TODO issue event
            $text = "Skill " . $this->name ." applied. Old value: " . $statValue;
            switch ($this->operator) {
                case "divide":
                    $statValue = $statValue / $this->value;
                    break;
                case "luck":
                    $statValue = $luck;
            }
            $text .= ". New value: " . $statValue ."\n";
            echo $text;
        }

        return $statValue;
    }

    private function feelingLucky(): int
    {
        return (rand(0, 99) < $this->probability ? 1 : 0);
    }
}