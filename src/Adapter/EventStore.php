<?php namespace Projectionist\Adapter;

use Projectionist\Adapter\Event;
use Projectionist\Adapter\EventStream;

interface EventStore
{
    public function hasEvents(): bool;

    public function latestEvent(): Event;

    public function getStream($last_event_id): EventStream;
}