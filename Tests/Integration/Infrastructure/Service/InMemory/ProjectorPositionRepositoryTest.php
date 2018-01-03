<?php namespace ProjectonistTests\Integration\Infrastructure\Projectionist\Service\InMemory;

use Projectionist\Services\ProjectorPositionLedger;
use Projectionist\Infrastructure\Services\InMemory;

class ProjectorPositionRepositoryTest extends \ProjectonistTests\Integration\Projectionist\Service\ProjectorPositionRepositoryTest
{
    protected function makeRepository(): ProjectorPositionLedger
    {
        return new \Projectionist\Adapter\InMemory\ProjectorPositionLedger();
    }
}