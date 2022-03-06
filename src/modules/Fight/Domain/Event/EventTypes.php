<?php

namespace modules\Fight\Domain\Event;

class EventTypes
{
    const EVENT_FIRST_ATTACK = 'first.attack';
    const EVENT_DECLARED_WINNER = 'declare.winner';

    const EVENTS_AVAILABLE = [self::EVENT_FIRST_ATTACK, self::EVENT_DECLARED_WINNER];
}