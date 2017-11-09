<?php namespace App\Services;

use App\ValueObjects\Event;
use Illuminate\Support\Collection;

interface EventStore
{
    public function latestEvent(): Event;

    public function getStream($last_event_id): Collection;
}