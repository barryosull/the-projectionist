<?php namespace Tests\Integration\App\Service;

use App\Services\EventStore;

abstract class EventStoreTest extends \PHPUnit_Framework_TestCase
{
    abstract protected function makeEventStore(): EventStore;

    const EVENT_ID = "event-id";

    public function test_get_event_stream()
    {
        $event_store = $this->makeEventStore();

        $stream = $event_store->getStream(self::EVENT_ID);

        $this->assertInstanceOf(EventStore\EventStream::class, $stream);
    }
}