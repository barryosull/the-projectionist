<?php namespace ProjectonistTests\Integration\Infrastructure\Service\InMemory;

use Projectionist\Adapter\ProjectorPositionLedger;

class ProjectorPositionRepositoryTest extends \ProjectonistTests\Integration\Service\ProjectorPositionRepositoryTest
{
    protected function makeRepository(): ProjectorPositionLedger
    {
        return new ProjectorPositionLedger\InMemory();
    }
}