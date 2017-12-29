<?php namespace Tests\Integration\Infrastructure\Projectionist\App\Service\InMemory;

use Projectionist\App\Services\ProjectorPositionLedger;
use Projectionist\Infrastructure\App\Services\InMemory;

class ProjectorPositionRepositoryTest extends \Tests\Integration\Projectionist\App\Service\ProjectorPositionRepositoryTest
{
    protected function makeRepository(): ProjectorPositionLedger
    {
        return new InMemory\ProjectorPositionLedger();
    }
}