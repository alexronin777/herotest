<?php

namespace modules\common;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    private ListenerProviderInterface $listenerProvider;
    private static EventDispatcher $instance;

    public function __construct()
    {
        $this->listenerProvider = new ListenerProvider();
    }

    public static function instance(): EventDispatcher
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function dispatch(object $event): object
    {
        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            return $event;
        }

        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            $listener($event);
        }

        return $event;
    }

    public function addListener(string $eventType, callable $callable)
    {
        $this->listenerProvider->addListener($eventType, $callable);
    }
}