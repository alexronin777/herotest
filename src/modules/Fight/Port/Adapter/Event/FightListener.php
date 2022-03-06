<?php

namespace modules\Fight\Port\Adapter\Event;

use modules\Fight\Domain\Event\EventTypes;
use modules\Fight\Domain\Event\FightEvent;
use modules\Fight\Domain\Fighter;

class FightListener
{
    public function __invoke(FightEvent $event): void
    {
        $source = $event->getSource();

        if ($source->getObject() instanceof Fighter) {
            $fighter = $source->getObject();
            switch ($source->getTrigger()) {
                case EventTypes::EVENT_FIRST_ATTACK:
                    echo "Fighter id " . $fighter->getFighterId() . " strikes first.\n";
                    break;
                case EventTypes::EVENT_DECLARED_WINNER:
                    echo "Fighter id " . $fighter->getFighterId() . " has won the battle.\n";
                    break;
            }
        }
    }
}