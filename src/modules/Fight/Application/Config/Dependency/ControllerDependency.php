<?php

namespace modules\Fight\Application\Config\Dependency;

use modules\common\Dependency;
use modules\Fight\Application\Service\FighterServiceInterface;
use modules\Fight\Application\Service\FightService;
use modules\Fight\Domain\FightRepositoryInterface;
use modules\Fight\Port\Adapter\Persistence\FightRepository;
use modules\Fight\Port\Adapter\Service\FighterService;

class ControllerDependency extends Dependency
{
    public function register(?array $configuration)
    {
        $this->Container->when(FightService::class)
            ->needs(FighterServiceInterface::class)
            ->give(FighterService::class);

        $this->Container->when(FighterService::class)
            ->needs(FightRepositoryInterface::class)
            ->give(FightRepository::class);
    }
}