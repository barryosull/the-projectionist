<?php namespace ProjectonistTests\Integration\Service\EventLog;

use Projectionist\Domain\Services\EventWrapper;
use Projectionist\Domain\Services\EventStream;

abstract class EventStreamTest extends \PHPUnit\Framework\TestCase
{
    abstract protected function makeEventStream(): EventStream;

    public function test_can_get_next_event()
    {
        $stream = $this->makeEventStream();

        $event = $stream->next();

        $this->assertInstanceOf(EventWrapper::class, $event);
    }

    public function test_returns_null_when_no_more_events()
    {
        $stream = $this->makeEventStream();

        $stream->next();

        $eventB = $stream->next();

        $this->assertNull($eventB);
    }
}