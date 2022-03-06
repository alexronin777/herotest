<?php

namespace modules\Fight\Domain;

class FightFilter
{
    private int $limit;
    private string $status;
    private int $fightId;

    public function __construct(int $limit, string $status, int $fightId)
    {
        $this->limit = $limit;
        $this->status = $status;
        $this->fightId = $fightId;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getFightId(): int
    {
        return $this->fightId;
    }
}