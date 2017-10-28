<?php namespace App\Services;

use Illuminate\Support\Collection;

interface EventStore
{
    public function latestEvent(): \stdClass;

    public function getStream($last_event_id): Collection;
}