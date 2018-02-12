<?php namespace ProjectonistTests\Integration\Infrastructure\Projectionist\Service\InMemory;

use Projectionist\Infrastructure\Services\InMemory;

class EventLogTest extends \ProjectonistTests\Integration\Projectionist\Service\EventLogTest
{
    protected function makeEventLog(): \Projectionist\Adapter\EventLog
    {
        return new \Projectionist\Adapter\EventLog\InMemory();
    }
}