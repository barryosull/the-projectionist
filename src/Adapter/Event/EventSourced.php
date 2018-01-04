<?php namespace Projectionist\Adapter\Event;

// TODO: Make compatible with actual snapshot concept

class EventSourced implements \Projectionist\Adapter\Event
{
    public $meta;
    public $content;

    public function __construct(EventSourced $snapshot)
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