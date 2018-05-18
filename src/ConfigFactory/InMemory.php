<?php namespace Projectionist\ConfigFactory;

use Projectionist\Config;
use Projectionist\Infra;
use Projectionist\Strategy\EventHandler;

class InMemory
{
    public function make(): Config
    {
        $projector_position_ledger = new Infra\ProjectorPositionLedger\InMemory();
        $event_log = new Infra\EventLog\InMemory();
        $event_handler = new EventHandler\ClassName();

        return new Config($projector_position_ledger, $event_log, $event_handler);
    }
}