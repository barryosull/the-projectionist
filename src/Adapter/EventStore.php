<?php namespace Projectionist\Adapter;

use Projectionist\Adapter\EventStore\Event;
use Projectionist\Adapter\EventStore\EventStream;

interface EventStore
{
    public function hasEvents(): bool;

    public function latestEvent(): Event;

    public function getStream($last_event_id): EventStream;
}