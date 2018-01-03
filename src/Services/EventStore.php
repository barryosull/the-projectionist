<?php namespace Projectionist\Services;

use Projectionist\Services\EventStore\Event;
use Projectionist\Services\EventStore\EventStream;

interface EventStore
{
    public function hasEvents(): bool;

    public function latestEvent(): Event;

    public function getStream($last_event_id): EventStream;
}