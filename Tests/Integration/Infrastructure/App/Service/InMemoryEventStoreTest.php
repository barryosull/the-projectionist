<?php namespace Tests\Integration\Infrastructure\App\Service;

use App\Services\EventStore;
use Infrastructure\App\Services\InMemoryEventStore;
use Tests\Integration\App\Service\EventStoreTest;

class InMemoryEventStoreTest extends EventStoreTest
{
    protected function makeEventStore(): EventStore
    {
        $store = new InMemoryEventStore();
        return $store;
    }
}