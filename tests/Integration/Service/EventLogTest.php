<?php namespace ProjectonistTests\Integration\Service;

use Projectionist\Domain\Services\EventLog;

abstract class EventLogTest extends \PHPUnit\Framework\TestCase
{
    abstract protected function makeEventLog(): EventLog;

    const EVENT_ID = "event-id";

    public function test_get_event_stream()
    {
        $event_log = $this->makeEventLog();

        $stream = $event_log->getStream(self::EVENT_ID);

        $this->assertInstanceOf(\Projectionist\Domain\Services\EventStream::class, $stream);
    }
}