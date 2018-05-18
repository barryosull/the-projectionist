<?php namespace Projectionist\App\ConfigFactory;

use Projectionist\App\Config;
use Projectionist\Infra;

class InMemory
{
    public function make(): Config
    {
        $projectorPositionLedger = new Infra\ProjectorPositionLedger\InMemory();
        $eventLog = new Infra\EventLog\InMemory();
        $eventHandler = new Infra\EventHandler\ClassName();

        return new Config($projectorPositionLedger, $eventLog, $eventHandler);
    }
}