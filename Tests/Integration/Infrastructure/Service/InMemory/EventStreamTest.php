<?php namespace Tests\Integration\Infrastructure\Projectionist\Service\InMemory;

use Projectionist\Services\EventStore\EventStream;
use Projectionist\Infrastructure\Services\InMemory;
use Tests\Fakes\Services\EventStore\ThingHappened;

class EventStreamTest extends \Tests\Integration\Projectionist\Service\EventStore\EventStreamTest
{
    protected function makeEventStream(): EventStream
    {
        $events = [
            new ThingHappened('id')
        ];

        return new \Projectionist\Adapter\InMemory\EventStream($events);
    }
}