<?php namespace Projectionist\ConfigFactory;

use Projectionist\Config;
use Projectionist\Adapter;
use Projectionist\Strategy\EventHandler;

class InMemory
{
    public function make(): Config
    {
        $projector_position_ledger = new Adapter\ProjectorPositionLedger\InMemory();
        $event_log = new Adapter\EventLog\InMemory();
        $event_handler = new EventHandler\ClassName();

        return new Config($projector_position_ledger, $event_log, $event_handler);
    }
}