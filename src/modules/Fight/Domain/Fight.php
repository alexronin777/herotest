<?php

namespace modules\Fight\Domain;

use modules\common\EventDispatcher;
use modules\Fight\Application\Exceptions\FightException;
use modules\Fight\Application\Exceptions\InvalidFighterException;
use modules\Fight\Domain\Event\EventTypes;
use modules\Fight\Domain\Event\FightEvent;
use modules\Fight\Port\Adapter\Event\FightListener;

class Fight
{
    private int $fightId = 0;
    private array $opponents;
    private string $status;
    private \DateTimeImmutable $date;

    /**
     * @throws FightException
     */
    public function __construct(array $opponents, \DateTimeImmutable $date)
    {
        if (empty($opponents)) {
            throw new FightException('Fighters needed');
        }

        /** @var Fighter $opponent */
        foreach ($opponents as $opponent) {
            if (!($opponent instanceof Fighter) || !$opponent->getFighterId()) {
                throw new FightException('You can\'t have a battle with unregistered fighters');
            }
        }
        $this->opponents = $opponents;
        $this->status = FightStatus::FIGHT_STATUS_PENDING;
        $this->date = $date;
    }

    public function isFinished(): bool
    {
        return ($this->status == FightStatus::FIGHT_STATUS_COMPLETE);
    }

    /**
     * @throws FightException
     * @throws InvalidFighterException
     */
    public function init()
    {
        EventDispatcher::instance()->addListener(FightEvent::class, new FightListener());

        $this->checkFightConditions();

        list($attacker, $opponent) = $this->getFightingRoles();

        while ($attacker->getHealth() > 0 && $opponent->getHealth() > 0) {
            $opponent->takeDamage($attacker->getStrength());
            if (!$attacker->isNext()) {
                $copy = $opponent;
                $opponent = $attacker;
                $attacker = $copy;
                unset($copy);
            }
        }
        $this->advanceStatus();
        $this->setFightersOutcome();
    }

    public function getFightId(): int
    {
        return $this->fightId;
    }

    public function setFightId(int $fightsHistoryId)
    {
        $this->fightId = $fightsHistoryId;
    }

    public function getOpponents(): array
    {
        return $this->opponents;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @throws FightException
     */
    private function advanceStatus()
    {
        if ($this->status == FightStatus::FIGHT_STATUS_PENDING) {
            $this->status = FightStatus::FIGHT_STATUS_COMPLETE;
        } else {
            throw new FightException('The fight is already in complete status');
        }
    }

    /**
     * @throws FightException
     */
    private function checkFightConditions(): void
    {
        if (!$this->fightId) {
            throw new FightException('Fight needs to be registered before battle can occur');
        }

        /** @var Fighter $opponent */
        foreach ($this->opponents as $opponent) {
            if (!$opponent->getFighterId()) {
                throw new FightException('Both opponents need to be registered before battle can occur');
            }
        }
    }

    /**
     * @return Fighter[]
     */
    private function getFightingRoles(): array
    {
        $attacker = false;
        $defender = false;

        /** @var Fighter $opponent */
        foreach ($this->opponents as $opponent) {
            if (!$attacker) {
                $attacker = $opponent;
                continue;
            }
            if (($opponent->getSpeed() > $attacker->getSpeed()) || ($opponent->getSpeed() == $attacker->getSpeed() && $opponent->getLuck() > $attacker->getLuck())) {
                $defender = $attacker;
                $attacker = $opponent;
                break;
            }
        }
        if (!$defender) {
            $defender = $opponent;
        }

        EventDispatcher::instance()->dispatch($this->getEventObj($attacker, EventTypes::EVENT_FIRST_ATTACK));

        return [$attacker, $defender];
    }

    /**
     * @throws InvalidFighterException
     */
    private function setFightersOutcome()
    {
        /** @var Fighter $opponent */
        foreach ($this->opponents as $opponent) {
            if ($opponent->getHealth() > 0) {
                $opponent->advanceFighterBattleStatus(OutcomeStatus::OUTCOME_WINNER);
                EventDispatcher::instance()->dispatch($this->getEventObj($opponent, EventTypes::EVENT_DECLARED_WINNER));
            } else {
                $opponent->advanceFighterBattleStatus(OutcomeStatus::OUTCOME_LOOSER);
            }
        }
    }

    private function getEventObj(object $object, string $eventType): FightEvent
    {
        if (false === in_array($eventType, EventTypes::EVENTS_AVAILABLE)) {
            throw new \InvalidArgumentException('Event not available');
        }

        return new FightEvent($object, $eventType);
    }
}