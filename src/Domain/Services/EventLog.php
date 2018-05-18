<?php namespace Projectionist\Domain\Services;

use Projectionist\Domain\Services\EventWrapper;
use Projectionist\Domain\Services\EventStream;

interface EventLog
{
    public function latestEvent(): EventWrapper;

    public function getStream($lastEventId): EventStream;
}