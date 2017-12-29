<?php namespace Tests\Fakes\Services\EventStore;

class ThingHappened implements \Projectionist\App\Services\EventStore\Event
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