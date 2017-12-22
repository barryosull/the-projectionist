<?php namespace Tests\Integration\Infrastructure\App\Service\InMemory;

use App\Services\EventStore\EventStream;
use Infrastructure\App\Services\InMemory;
use Tests\Fakes\Services\EventStore\ThingHappened;

class EventStreamTest extends \Tests\Integration\App\Service\EventStore\EventStreamTest
{
    protected function makeEventStream(): EventStream
    {
        $events = [
            new ThingHappened('id')
        ];

        return new InMemory\EventStream($events);
    }
}