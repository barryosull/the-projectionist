<?php namespace Tests\Integration\Infrastructure\Projectionist\Service\InMemory;

use Projectionist\Infrastructure\Services\InMemory;

class EventStoreTest extends \Tests\Integration\Projectionist\Service\EventStoreTest
{
    protected function makeEventStore(): \Projectionist\Services\EventStore
    {
        return new \Projectionist\Adapter\InMemory\EventStore();
    }
}