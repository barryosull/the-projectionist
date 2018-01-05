<?php namespace Projectionist\Adapter\EventWrapper;

// TODO: Move into it's own namespace/repo, this is just here as a POC
class EventSourcedFramework implements \Projectionist\Adapter\EventWrapper
{
    public $meta;
    public $content;

    public function __construct(EventSourcedFramework $snapshot)
    {
        $this->meta = $snapshot;
    }

    public function wrappedEvent()
    {
        return $this->content;
    }

    public function id()
    {
        return $this->meta->id();
    }
}