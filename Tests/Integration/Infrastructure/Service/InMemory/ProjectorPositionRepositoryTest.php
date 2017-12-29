<?php namespace Tests\Integration\Infrastructure\Projectionist\Service\InMemory;

use Projectionist\Services\ProjectorPositionLedger;
use Projectionist\Infrastructure\Services\InMemory;

class ProjectorPositionRepositoryTest extends \Tests\Integration\Projectionist\Service\ProjectorPositionRepositoryTest
{
    protected function makeRepository(): ProjectorPositionLedger
    {
        return new InMemory\ProjectorPositionLedger();
    }
}