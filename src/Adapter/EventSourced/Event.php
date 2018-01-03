<?php namespace Projectionist\Adapter\EventSourced;

// TODO: Make compatible with actual snapshot concept
use Projectionist\Adapter\EventSourced\Snapshot;

class Event implements \Projectionist\Services\EventStore\Event
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