<?php namespace Projectionist\Infra\EventWrapper;

class Identifiable implements \Projectionist\Domain\Services\EventWrapper
{
    private $wrapped_event;

    public function __construct($event)
    {
        $this->wrapped_event = $event;
    }

    public function id()
    {
        return $this->wrapped_event->id();
    }

    public function wrappedEvent()
    {
        return $this->wrapped_event;
    }
}