<?php namespace Tests\Integration\Infrastructure\Projectionist\App\Service\InMemory;

use Projectionist\Infrastructure\App\Services\InMemory;

class EventStoreTest extends \Tests\Integration\Projectionist\App\Service\EventStoreTest
{
    protected function makeEventStore(): \Projectionist\App\Services\EventStore
    {
        return new InMemory\EventStore();
    }
}