<?php namespace ProjectonistTests\Integration\Infrastructure\Projectionist\Service\InMemory;

use Projectionist\Adapter\ProjectorPositionLedger;
use Projectionist\Infrastructure\Services\InMemory;

class ProjectorPositionRepositoryTest extends \ProjectonistTests\Integration\Projectionist\Service\ProjectorPositionRepositoryTest
{
    protected function makeRepository(): ProjectorPositionLedger
    {
        return new ProjectorPositionLedger\InMemory();
    }
}