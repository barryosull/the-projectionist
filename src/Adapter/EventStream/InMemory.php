<?php namespace Projectionist\Adapter\EventStream;

use Projectionist\Adapter\Event\InMemory;
use Projectionist\Services;
use Illuminate\Support\Collection;

class InMemory implements \Projectionist\Adapter\EventStream
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
            return new InMemory($event);
        }
        return null;
    }
}