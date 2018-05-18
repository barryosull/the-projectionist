<?php namespace Projectionist\App\ConfigFactory;

use Projectionist\App\Config;
use Projectionist\Infra;

class Redis
{
    private $projectorPositionLedger;

    public function __construct(Infra\ProjectorPositionLedger\Redis $projector_position_ledger)
    {
        $this->projectorPositionLedger = $projector_position_ledger;
    }

    public function make(): Config
    {
        $eventLog = new Infra\EventLog\InMemory();
        $eventHandler = new Infra\EventHandler\ClassName();

        return new Config($this->projectorPositionLedger, $eventLog, $eventHandler);
    }
}