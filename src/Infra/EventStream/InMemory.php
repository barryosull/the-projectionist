<?php namespace Projectionist\Infra\EventStream;

use Projectionist\Infra\EventWrapper\Identifiable;
use Illuminate\Support\Collection;

class InMemory implements \Projectionist\Domain\Services\EventStream
{
    private $events;

    public function __construct(array $events)
    {
        $this->events = new Collection($events);
    }

    /** @return \Projectionist\Domain\Services\EventWrapper */
    public function next()
    {
        $event = $this->events->shift();
        if ($event) {
            return new Identifiable($event);
        }
        return null;
    }
}