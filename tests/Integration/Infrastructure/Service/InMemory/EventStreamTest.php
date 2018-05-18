<?php namespace ProjectonistTests\Integration\Infrastructure\Service\InMemory;

use Projectionist\Domain\Services\EventStream;
use Projectionist\Infra\EventStream\InMemory;
use ProjectonistTests\Fakes\Services\EventLog\ThingHappened;

class EventStreamTest extends \ProjectonistTests\Integration\Service\EventLog\EventStreamTest
{
    protected function makeEventStream(): EventStream
    {
        $events = [
            new ThingHappened('id')
        ];

        return new InMemory($events);
    }
}