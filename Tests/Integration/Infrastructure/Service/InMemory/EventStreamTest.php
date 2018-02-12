<?php namespace ProjectonistTests\Integration\Infrastructure\Projectionist\Service\InMemory;

use Projectionist\Adapter\EventStream;
use Projectionist\Infrastructure\Services\InMemory;
use ProjectonistTests\Fakes\Services\EventLog\ThingHappened;

class EventStreamTest extends \ProjectonistTests\Integration\Projectionist\Service\EventLog\EventStreamTest
{
    protected function makeEventStream(): EventStream
    {
        $events = [
            new ThingHappened('id')
        ];

        return new EventStream\InMemory($events);
    }
}