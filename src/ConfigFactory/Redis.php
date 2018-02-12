<?php namespace Projectionist\ConfigFactory;

use Projectionist\Config;
use Projectionist\Adapter;
use Projectionist\Strategy\EventHandler;

class Redis
{
    private $projector_position_ledger;

    public function __construct(Adapter\ProjectorPositionLedger\Redis $projector_position_ledger)
    {
        $this->projector_position_ledger = $projector_position_ledger;
    }

    public function make(): Config
    {
        $event_log = new Adapter\EventLog\InMemory();
        $event_handler = new EventHandler\ClassName();

        return new Config($this->projector_position_ledger, $event_log, $event_handler);
    }
}