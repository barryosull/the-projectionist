<?php namespace Tests\Integration\Infrastructure\App\Service\EventStore;

use App\Services\EventStore\EventStream;
use Infrastructure\App\Services\InMemoryEventStream;
use Tests\Fakes\Event;
use Tests\Integration\App\Service\EventStore\EventStreamTest;

class InMemoryEventStreamTest extends EventStreamTest
{
    protected function makeEventStream(): EventStream
    {
        $events = [
            new Event('id', 'type')
        ];

        return new InMemoryEventStream($events);
    }
}