<?php namespace Tests\Integration\Infrastructure\App\Service;

use App\Services\ProjectorPositionRepository;
use Infrastructure\App\Services\InMemoryProjectorPositionRepository;
use Tests\Integration\App\Service\ProjectorPositionRepositoryTest;

class InMemoryProjectorPositionRepositoryTest extends ProjectorPositionRepositoryTest
{
    protected function makeRepository(): ProjectorPositionRepository
    {
        return new InMemoryProjectorPositionRepository();
    }
}