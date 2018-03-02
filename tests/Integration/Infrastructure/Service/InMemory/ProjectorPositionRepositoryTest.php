<?php namespace ProjectonistTests\Integration\Infrastructure\Projectionist\Service\InMemory;

use Projectionist\Adapter\ProjectorPositionLedger;

class ProjectorPositionRepositoryTest extends \ProjectonistTests\Integration\Projectionist\Service\ProjectorPositionRepositoryTest
{
    protected function makeRepository(): ProjectorPositionLedger
    {
        return new ProjectorPositionLedger\InMemory();
    }
}