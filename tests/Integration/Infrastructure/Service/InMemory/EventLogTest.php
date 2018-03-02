<?php namespace ProjectonistTests\Integration\Infrastructure\Service\InMemory;

class EventLogTest extends \ProjectonistTests\Integration\Service\EventLogTest
{
    protected function makeEventLog(): \Projectionist\Adapter\EventLog
    {
        return new \Projectionist\Adapter\EventLog\InMemory();
    }
}