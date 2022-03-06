<?php

namespace modules\Fight\Domain\Event;

use modules\common\Event;

class FightEvent extends Event
{
    private object $source;

    public function __construct(object $object, string $trigger)
    {
        $carrier = new EventCarrier($object, $trigger);
        $this->source = $carrier;
    }

    public function getSource(): object
    {
        return $this->source;
    }
}