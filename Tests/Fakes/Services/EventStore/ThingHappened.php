<?php namespace ProjectonistTests\Fakes\Services\EventStore;

class ThingHappened implements \Projectionist\Services\EventStore\Event
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id()
    {
        return $this->id;
    }

    public function content()
    {
        return $this;
    }
}