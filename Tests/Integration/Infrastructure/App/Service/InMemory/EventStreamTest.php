<?php namespace Tests\Integration\Infrastructure\Projectionist\App\Service\InMemory;

use Projectionist\App\Services\EventStore\EventStream;
use Projectionist\Infrastructure\App\Services\InMemory;
use Tests\Fakes\Services\EventStore\ThingHappened;

class EventStreamTest extends \Tests\Integration\Projectionist\App\Service\EventStore\EventStreamTest
{
    protected function makeEventStream(): EventStream
    {
        $events = [
            new ThingHappened('id')
        ];

        return new InMemory\EventStream($events);
    }
}