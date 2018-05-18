<?php namespace Projectionist\App\ConfigFactory;

use Projectionist\App\Config;
use Projectionist\Infra;

class InMemory
{
    public function make(): Config
    {
        $projector_position_ledger = new Infra\ProjectorPositionLedger\InMemory();
        $event_log = new Infra\EventLog\InMemory();
        $event_handler = new Infra\EventHandler\ClassName();

        return new Config($projector_position_ledger, $event_log, $event_handler);
    }
}