<?php namespace Projectionist\AdapterFactory\InMemory;

use Projectionist\Services;
use Illuminate\Support\Collection;

class EventStream implements Services\EventStore\EventStream
{
    private $events;

    public function __construct(array $events)
    {
        $this->events = new Collection($events);
    }

    /** @return Services\EventStore\Event */
    public function next()
    {
        $event = $this->events->shift();
        if ($event) {
            return new Event($event);
        }
        return null;
    }
}