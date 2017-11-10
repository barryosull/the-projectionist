<?php namespace Infrastructure\EventSourced\Event;

// TODO: Make compatible with snapshot concept
// TODO: Possibly build integration interfaces, allow other services to define how they plug into this system

use Infrastructure\EventSourced\Snapshot\Snapshot;

class Event implements \App\Services\EventStore\Event
{
    private $snapshot;

    public function __construct(Snapshot $snapshot)
    {
        $this->snapshot = $snapshot;
    }

    public function id()
    {
        return $this->snapshot->id();
    }

    private function handlerFunctionName(): string
    {
        $type = $this->snapshot->type();
        $event_type_snakecase = str_replace(".", "_", $type);
        return 'when_'.$event_type_snakecase;
    }

    public function playIntoProjector($projector)
    {
        $method = $this->handlerFunctionName();
        if (method_exists($projector, $method)) {
            $projector->$method($this->snapshot->schema(), $this->snapshot);
        }
    }
}