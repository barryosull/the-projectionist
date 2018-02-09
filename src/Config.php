<?php namespace Projectionist;

use Projectionist\Adapter\EventStore;
use Projectionist\Strategy\EventHandler;
use Projectionist\Adapter\ProjectorPositionLedger;

class Config {

    private $projector_position_ledger;
    private $event_store;
    private $event_handler;

    public function __construct(
        ProjectorPositionLedger $projector_position_ledger,
        EventStore $event_store,
        EventHandler $event_handler
    ) {
        $this->projector_position_ledger = $projector_position_ledger;
        $this->event_store = $event_store;
        $this->event_handler = $event_handler;
    }

    public function projectorPositionLedger(): ProjectorPositionLedger
    {
        return $this->projector_position_ledger;
    }

    public function eventStore(): EventStore
    {
        return $this->event_store;
    }

    public function eventHandler(): EventHandler
    {
        return $this->event_handler;
    }
}