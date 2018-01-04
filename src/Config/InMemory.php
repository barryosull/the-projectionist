<?php namespace Projectionist\Config;

use Projectionist\Config;
use Projectionist\Strategy\EventHandler;

class InMemory implements Config
{
    private $event_store;
    private $projector_player;
    private $projector_position_ledger;

    public function __construct()
    {
        $this->event_store = new Config\InMemory\EventStore();
        $this->projector_player = new EventHandler\ClassName();
        $this->projector_position_ledger = new \Projectionist\Adapter\ProjectorPositionLedger\InMemory();
    }

    public function eventStore(): \Projectionist\Adapter\EventStore
    {
        return $this->event_store;
    }

    public function projectorPlayer(): \Projectionist\Strategy\EventHandler
    {
        return $this->projector_player;
    }

    public function projectorPositionLedger(): \Projectionist\Adapter\ProjectorPositionLedger
    {
        return $this->projector_position_ledger;
    }
}