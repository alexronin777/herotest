<?php

namespace unit\modules\Fight\Application\Service;

use modules\Fight\Application\Service\FighterServiceInterface;
use modules\Fight\Application\Service\FightService;
use modules\Fight\Domain\Fight;
use modules\Fight\Domain\Fighter;
use modules\Fight\Domain\FightEvaluationDto;
use modules\Fight\Domain\Skill;
use modules\Fight\Domain\SkillCollection;
use modules\Fight\Domain\SkillTypes;
use PHPUnit\Framework\TestCase;

class FightServiceTest extends TestCase
{
    public function testFightEvaluationFights()
    {
        $cmd = new FightEvaluationDto(1, 1);
        $service = $this->getMockBuilder(FighterServiceInterface::class)
            ->getMock();
        $fight = $this->getValidFight();
        $fight->setFightId(1);
        $service->expects($this->once())
            ->method('pickFights')
            ->with($cmd)
            ->willReturn([
                $fight
            ]);
        $service->expects($this->once())
            ->method('storeFightResult')
            ->withAnyParameters();

        $fightService = new FightService($service);
        $fightService->evaluateFights($cmd);
    }

    private function getValidFight(): Fight
    {
        $opponents = $this->getValidOpponents();
        foreach ($opponents as $opponent) {
            $opponent->setFighterId(10);
        }

        return new Fight($opponents, new \DateTimeImmutable());
    }

    private function getValidOpponents(): array
    {
        $skills = new SkillCollection();
        $skills->add($this->getValidSkill());
        $skills->add($this->getValidSkill());
        $fighter1 = new Fighter(10, 10, 10, 10, 10, $skills);
        $fighter1->setFighterId(1);

        $fighter2 = new Fighter(10, 50, 10, 10, 15, $skills);
        $fighter2->setFighterId(2);

        return [
            $fighter1,
            $fighter2
        ];
    }

    private function getValidSkill(): Skill
    {
        return new Skill(10, SkillTypes::ATTACK_TYPE, 'test', 'test', 10, 'luck', 10);
    }
}