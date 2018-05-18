<?php namespace ProjectonistTests\Integration\Infrastructure\Service\Redis;

use Projectionist\Domain\Services\ProjectorPositionLedger;
use Predis\Client;

// TODO: Test this works, until then disable
class ProjectorPositionRepositoryTest extends \ProjectonistTests\Integration\Service\ProjectorPositionRepositoryTest
{
    protected function makeRepository(): ProjectorPositionLedger
    {
        $client = new Client();
        return new ProjectorPositionLedger\Redis($client);
    }
}