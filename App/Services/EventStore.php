<?php namespace App\Services;

use App\ValueObjects\Event;
use App\ValueObjects\EventCollection;

interface EventStore
{
    public function latestEvent(): Event;

    public function getStream($last_event_id): EventCollection;
}