<?php namespace Infrastructure\EventSourced\Event;

// TODO: Make compatible with actual snapshot concept
use Infrastructure\EventSourced\Snapshot\Snapshot;

class Event implements \App\Services\EventStore\Event
{
    public $meta;
    public $content;

    public function __construct(Snapshot $snapshot)
    {
        $this->meta = $snapshot;
    }

    public function content()
    {
        return $this->content;
    }

    public function id()
    {
        return $this->meta->id();
    }
}