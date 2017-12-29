<?php namespace Projectionist\App\Services;

use Projectionist\App\Services\EventStore\Event;
use Projectionist\App\Services\EventStore\EventStream;

interface EventStore
{
    public function latestEvent(): Event;

    public function getStream($last_event_id): EventStream;
}