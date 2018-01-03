<?php namespace ProjectonistTests\Integration\Infrastructure\Projectionist\Service\InMemory;

use Projectionist\Infrastructure\Services\InMemory;

class EventStoreTest extends \ProjectonistTests\Integration\Projectionist\Service\EventStoreTest
{
    protected function makeEventStore(): \Projectionist\Services\EventStore
    {
        return new \Projectionist\Adapter\InMemory\EventStore();
    }
}