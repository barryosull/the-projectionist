<?php namespace Tests\Integration\Infrastructure\App\Service\InMemory;

use App\Services\ProjectorPositionLedger;
use Infrastructure\App\Services\InMemory;

class ProjectorPositionRepositoryTest extends \Tests\Integration\App\Service\ProjectorPositionRepositoryTest
{
    protected function makeRepository(): ProjectorPositionLedger
    {
        return new InMemory\ProjectorPositionLedger();
    }
}