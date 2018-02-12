<?php namespace Projectionist\Adapter;

use Projectionist\Adapter\EventWrapper;
use Projectionist\Adapter\EventStream;

interface EventLog
{
    public function hasEvents(): bool;

    public function latestEvent(): EventWrapper;

    public function getStream($last_event_id): EventStream;
}