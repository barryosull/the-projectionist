<?php namespace ProjectonistTests\Integration\Infrastructure\Projectionist\Service\Redis;

use Projectionist\Adapter\ProjectorPositionLedger;
use Predis;

// TODO: Test this works, until then disable
class ProjectorPositionRepositoryTest extends \ProjectonistTests\Integration\Projectionist\Service\ProjectorPositionRepositoryTest
{
    protected function makeRepository(): ProjectorPositionLedger
    {
        $client = new Predis\Client();
        return new ProjectorPositionLedger\Redis($client);
    }
}