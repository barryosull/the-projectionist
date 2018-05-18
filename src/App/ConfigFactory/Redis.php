<?php namespace Projectionist\App\ConfigFactory;

use Projectionist\App\Config;
use Projectionist\Infra;

class Redis
{
    private $projector_position_ledger;

    public function __construct(Infra\ProjectorPositionLedger\Redis $projector_position_ledger)
    {
        $this->projector_position_ledger = $projector_position_ledger;
    }

    public function make(): Config
    {
        $event_log = new Infra\EventLog\InMemory();
        $event_handler = new Infra\EventHandler\ClassName();

        return new Config($this->projector_position_ledger, $event_log, $event_handler);
    }
}