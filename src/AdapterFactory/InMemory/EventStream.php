<?php namespace Projectionist\AdapterFactory\InMemory;

use Projectionist\Services;
use Illuminate\Support\Collection;

class EventStream implements \Projectionist\Adapter\EventStore\EventStream
{
    private $events;

    public function __construct(array $events)
    {
        $this->events = new Collection($events);
    }

    /** @return \Projectionist\Adapter\EventStore\Event */
    public function next()
    {
        $event = $this->events->shift();
        if ($event) {
            return new Event($event);
        }
        return null;
    }
}