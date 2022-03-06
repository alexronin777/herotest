<?php

namespace modules\Fight\Domain;

class FightEvaluationDto
{
    private int $limit;
    private ?int $fightId;

    public function __construct(int $limit, ?int $fightId)
    {
        $this->limit = $limit;
        $this->fightId = $fightId;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getFightId(): ?int
    {
        return $this->fightId;
    }
}