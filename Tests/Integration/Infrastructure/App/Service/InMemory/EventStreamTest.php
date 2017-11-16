<?php namespace Tests\Integration\Infrastructure\App\Service\InMemory;

use App\Services\EventStore\EventStream;
use Infrastructure\App\Services\InMemory;
use Tests\Fakes\Services\EventStore\Event;

class EventStreamTest extends \Tests\Integration\App\Service\EventStore\EventStreamTest
{
    protected function makeEventStream(): EventStream
    {
        $events = [
            new Event('id', 'type')
        ];

        return new InMemory\EventStream($events);
    }
}