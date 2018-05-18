<?php namespace Projectionist\App;

use Projectionist\Domain\Services\EventLog;
use Projectionist\Domain\Strategy\EventHandler;
use Projectionist\Domain\Services\ProjectorPositionLedger;

class Config {

    private $projectorPositionLedger;
    private $eventLog;
    private $eventHandler;

    public function __construct(
        ProjectorPositionLedger $projectorPositionLedger,
        EventLog $eventLog,
        EventHandler $eventHandler
    ) {
        $this->projectorPositionLedger = $projectorPositionLedger;
        $this->eventLog = $eventLog;
        $this->eventHandler = $eventHandler;
    }

    public function projectorPositionLedger(): ProjectorPositionLedger
    {
        return $this->projectorPositionLedger;
    }

    public function eventLog(): EventLog
    {
        return $this->eventLog;
    }

    public function eventHandler(): EventHandler
    {
        return $this->eventHandler;
    }
}