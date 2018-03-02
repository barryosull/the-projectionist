<?php namespace ProjectonistTests\Integration\Projectionist\Service;

use Projectionist\Adapter\EventLog;

abstract class EventLogTest extends \PHPUnit\Framework\TestCase
{
    abstract protected function makeEventLog(): EventLog;

    const EVENT_ID = "event-id";

    public function test_get_event_stream()
    {
        $event_log = $this->makeEventLog();

        $stream = $event_log->getStream(self::EVENT_ID);

        $this->assertInstanceOf(\Projectionist\Adapter\EventStream::class, $stream);
    }
}