<?php

namespace modules\Fight\Domain\Event;

class EventCarrier
{
    private object $object;
    private string $trigger;

    public function __construct(object $object, string $trigger)
    {
        $this->object = $object;
        $this->trigger = $trigger;
    }

    public function getObject(): object
    {
        return $this->object;
    }

    public function getTrigger(): string
    {
        return $this->trigger;
    }
}