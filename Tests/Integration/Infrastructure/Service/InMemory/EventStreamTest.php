<?php namespace ProjectonistTests\Integration\Infrastructure\Projectionist\Service\InMemory;

use Projectionist\Services\EventStore\EventStream;
use Projectionist\Infrastructure\Services\InMemory;
use ProjectonistTests\Fakes\Services\EventStore\ThingHappened;

class EventStreamTest extends \ProjectonistTests\Integration\Projectionist\Service\EventStore\EventStreamTest
{
    protected function makeEventStream(): EventStream
    {
        $events = [
            new ThingHappened('id')
        ];

        return new \Projectionist\Adapter\InMemory\EventStream($events);
    }
}