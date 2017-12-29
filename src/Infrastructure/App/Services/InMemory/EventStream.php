<?php namespace Projectionist\Infrastructure\App\Services\InMemory;

use Projectionist\App\Services\EventStore\Event;
use Illuminate\Support\Collection;

class EventStream implements \Projectionist\App\Services\EventStore\EventStream
{
    private $events;

    public function __construct(array $events)
    {
        $this->events = new Collection($events);
    }

    /** @return Event */
    public function next()
    {
        return $this->events->shift();
    }
}