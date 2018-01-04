<?php namespace Projectionist\Config\InMemory;

use Projectionist\Services;
use Illuminate\Support\Collection;

class EventStream implements \Projectionist\Adapter\EventStream
{
    private $events;

    public function __construct(array $events)
    {
        $this->events = new Collection($events);
    }

    /** @return \Projectionist\Adapter\Event */
    public function next()
    {
        $event = $this->events->shift();
        if ($event) {
            return new Event($event);
        }
        return null;
    }
}