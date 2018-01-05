<?php namespace Projectionist\Adapter;

use Projectionist\Adapter\EventWrapper;
use Projectionist\Adapter\EventStream;

interface EventStore
{
    public function hasEvents(): bool;

    public function latestEvent(): EventWrapper;

    public function getStream($last_event_id): EventStream;
}