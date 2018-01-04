<?php namespace ProjectonistTests\Integration\Infrastructure\Projectionist\Service\InMemory;

use Projectionist\Adapter\EventStream;
use Projectionist\Infrastructure\Services\InMemory;
use ProjectonistTests\Fakes\Services\EventStore\ThingHappened;

class EventStreamTest extends \ProjectonistTests\Integration\Projectionist\Service\EventStore\EventStreamTest
{
    protected function makeEventStream(): EventStream
    {
        $events = [
            new ThingHappened('id')
        ];

        return new \Projectionist\Config\InMemory\EventStream($events);
    }
}