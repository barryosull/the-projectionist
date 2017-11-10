<?php namespace Infrastructure\App\Services;

use App\Services\EventStore\Event;
use App\Services\EventStore\EventStream;
use Illuminate\Support\Collection;

class InMemoryEventStream implements EventStream
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