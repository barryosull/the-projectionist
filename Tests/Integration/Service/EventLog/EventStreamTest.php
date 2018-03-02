<?php namespace ProjectonistTests\Integration\Projectionist\Service\EventLog;

use Projectionist\Adapter\EventWrapper;
use Projectionist\Adapter\EventStream;

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

        $event_b = $stream->next();

        $this->assertNull($event_b);
    }
}