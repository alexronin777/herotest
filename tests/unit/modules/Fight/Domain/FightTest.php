<?php

namespace unit\modules\Fight\Domain;

use modules\Fight\Application\Exceptions\FightException;
use modules\Fight\Domain\Fight;
use modules\Fight\Domain\Fighter;
use modules\Fight\Domain\FightStatus;
use modules\Fight\Domain\Skill;
use modules\Fight\Domain\SkillCollection;
use modules\Fight\Domain\SkillTypes;
use PHPUnit\Framework\TestCase;

class FightTest extends TestCase
{
    public function testInitExceptionFighters()
    {
        $this->expectException(FightException::class);
        $skills = new SkillCollection();
        $skills->add($this->getValidSkill());
        $skills->add($this->getValidSkill());

        $opponents = [
            new Fighter(10, 10, 10, 10, 10, $skills),
            new Fighter(10, 10, 10, 10, 10, $skills)
        ];
        $fight = new Fight($opponents, new \DateTimeImmutable());
        $fight->setFightId(1);
        $fight->init();
    }

    public function testInitExceptionFight()
    {
        $this->expectException(FightException::class);

        $opponents = $this->getValidOpponents();
        $fight = new Fight($opponents, new \DateTimeImmutable());
        $fight->init();
    }

    public function testInitSuccess()
    {
        $fight = $this->getValidFight();
        $fight->setFightId(1);

        $fight->init();

        $this->assertTrue($fight->isFinished());
        $this->assertSame('COMPLETE', $fight->getStatus());

    }

    /**
     * @dataProvider getFaultyFightData
     */
    public function testInvalidConstructor($opponents, $date)
    {
        $this->expectException(FightException::class);

        $fight = new Fight($opponents, $date);
    }

    public function testValidConstructor()
    {
        $fight = $this->getValidFight();
        $fight->setFightId(1);

        $this->assertInstanceOf(Fight::class, $fight);
        $this->assertSame($fight->getStatus(), FightStatus::FIGHT_STATUS_PENDING);
        $this->assertIsNumeric($fight->getFightId());
        $this->assertIsArray($fight->getOpponents());
        $this->assertInstanceOf(\DateTimeImmutable::class, $fight->getDate());
        $this->assertFalse($fight->isFinished());
    }

    public function getFaultyFightData(): iterable
    {
        return [
            [
                'opponents' => $this->getValidOpponents(),
                'date' => new \DateTimeImmutable(),
            ],
            [
                'opponents' => [],
                'date' => new \DateTimeImmutable()
            ],
            [
                'opponents' => [1, 'sdsdfd'],
                'date' => new \DateTimeImmutable()
            ]
        ];
    }

    private function getValidFight(): Fight
    {
        $opponents = $this->getValidOpponents();
        foreach ($opponents as $opponent) {
            $opponent->setFighterId(random_int(1, 100));
        }

        return new Fight($opponents, new \DateTimeImmutable());
    }

    private function getValidOpponents(): array
    {
        $skills = new SkillCollection();
        $skills->add($this->getValidSkill());
        $skills->add($this->getValidSkill());

        return [
            new Fighter(random_int(1, 100), random_int(1, 100), random_int(1, 100), random_int(1, 100), random_int(1, 100), $skills),
            new Fighter(random_int(1, 100), random_int(1, 100), random_int(1, 100), random_int(1, 100), random_int(1, 100), $skills)
        ];
    }

    private function getValidSkill(): Skill
    {
        return new Skill(random_int(1, 100), SkillTypes::ATTACK_TYPE, 'test', 'test', 10, 'luck', 10);
    }
}