<?php

namespace modules\Fight\Port\Adapter\Controller;

use modules\Fight\Application\Exceptions\FightException;
use modules\Fight\Application\Exceptions\InvalidFighterException;
use modules\Fight\Application\Service\FightService;

class FightController
{
    private FightService $service;

    public function __construct(FightService $service)
    {
        $this->service = $service;
    }

    public function enqueueFight(array $fightDetails)
    {
        try {
            $opponents = $this->prepareData($fightDetails);
            $this->service->enqueueFight($opponents);
        } catch (InvalidFighterException | FightException $e) {
            print_r($e->getMessage());
        }
    }

    private function prepareData(array $details): array
    {
        parse_str($details['fighter1'], $fighter1);
        parse_str($details['fighter2'], $fighter2);

        return [$fighter1, $fighter2];
    }
}