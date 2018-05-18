<?php namespace ProjectonistTests\Integration\Infrastructure\Service\InMemory;

class EventLogTest extends \ProjectonistTests\Integration\Service\EventLogTest
{
    protected function makeEventLog(): \Projectionist\Domain\Services\EventLog
    {
        return new \Projectionist\Infra\EventLog\InMemory();
    }
}