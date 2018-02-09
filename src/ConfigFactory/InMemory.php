<?php namespace Projectionist\ConfigFactory;

use Projectionist\Config;
use Projectionist\Adapter;
use Projectionist\Strategy\EventHandler;

class InMemory
{
    public function make(): Config
    {
        $projector_position_ledger = new Adapter\ProjectorPositionLedger\InMemory();
        $event_store = new Adapter\EventStore\InMemory();
        $event_handler = new EventHandler\ClassName();

        return new Config($projector_position_ledger, $event_store, $event_handler);
    }
}