<?php namespace Projectionist;

use Projectionist\Adapter\EventLog;
use Projectionist\Strategy\EventHandler;
use Projectionist\Adapter\ProjectorPositionLedger;

class Config {

    private $projector_position_ledger;
    private $event_log;
    private $event_handler;

    public function __construct(
        ProjectorPositionLedger $projector_position_ledger,
        EventLog $event_log,
        EventHandler $event_handler
    ) {
        $this->projector_position_ledger = $projector_position_ledger;
        $this->event_log = $event_log;
        $this->event_handler = $event_handler;
    }

    public function projectorPositionLedger(): ProjectorPositionLedger
    {
        return $this->projector_position_ledger;
    }

    public function eventLog(): EventLog
    {
        return $this->event_log;
    }

    public function eventHandler(): EventHandler
    {
        return $this->event_handler;
    }
}