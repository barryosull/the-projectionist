<?php namespace App\Services;

use App\Services\EventStore\Event;
use App\Services\EventStore\EventStream;

interface EventStore
{
    public function latestEvent(): Event;

    public function getStream($last_event_id): EventStream;
}