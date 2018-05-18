<?php namespace ProjectonistTests\Integration\Infrastructure\Service\InMemory;

use Projectionist\Domain\Services\EventStream;
use ProjectonistTests\Fakes\Services\EventLog\ThingHappened;

class EventStreamTest extends \ProjectonistTests\Integration\Service\EventLog\EventStreamTest
{
    protected function makeEventStream(): EventStream
    {
        $events = [
            new ThingHappened('id')
        ];

        return new EventStream\InMemory($events);
    }
}