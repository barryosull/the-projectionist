<?php namespace Projectionist\Infrastructure\EventSourced\Event;

// TODO: Make compatible with actual snapshot concept
use Projectionist\Infrastructure\EventSourced\Snapshot\Snapshot;

class Event implements \Projectionist\App\Services\EventStore\Event
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