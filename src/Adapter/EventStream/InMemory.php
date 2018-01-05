<?php namespace Projectionist\Adapter\EventStream;

use Projectionist\Adapter\EventWrapper\Identifiable;
use Illuminate\Support\Collection;

class InMemory implements \Projectionist\Adapter\EventStream
{
    private $events;

    public function __construct(array $events)
    {
        $this->events = new Collection($events);
    }

    /** @return \Projectionist\Adapter\EventWrapper */
    public function next()
    {
        $event = $this->events->shift();
        if ($event) {
            return new Identifiable($event);
        }
        return null;
    }
}