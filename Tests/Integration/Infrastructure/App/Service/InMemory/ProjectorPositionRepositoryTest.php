<?php namespace Tests\Integration\Infrastructure\App\Service\InMemory;

use App\Services\ProjectorPositionRepository;
use Infrastructure\App\Services\InMemory;

class ProjectorPositionRepositoryTest extends \Tests\Integration\App\Service\ProjectorPositionRepositoryTest
{
    protected function makeRepository(): ProjectorPositionRepository
    {
        return new InMemory\ProjectorPositionRepository();
    }
}