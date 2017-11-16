<?php namespace Tests\Integration\Infrastructure\App\Service\InMemory;

use Infrastructure\App\Services\InMemory;

class EventStoreTest extends \Tests\Integration\App\Service\EventStoreTest
{
    protected function makeEventStore(): \App\Services\EventStore
    {
        return new InMemory\EventStore();
    }
}