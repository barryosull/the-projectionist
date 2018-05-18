<?php namespace ProjectonistTests\Integration\Infrastructure\Service\InMemory;

use Projectionist\Infra\ProjectorPositionLedger\InMemory;
use Projectionist\Domain\Services\ProjectorPositionLedger;

class ProjectorPositionRepositoryTest extends \ProjectonistTests\Integration\Service\ProjectorPositionRepositoryTest
{
    protected function makeRepository(): ProjectorPositionLedger
    {
        return new InMemory();
    }
}