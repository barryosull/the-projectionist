<?php namespace Projectionist\AdapterFactory;

use Projectionist\AdapterFactory;
use Projectionist\Adapter\ProjectorPlayer;

class EventSourced implements AdapterFactory
{
    private $event_store;
    private $projector_player;
    private $projector_position_ledger;

    public function __construct()
    {
        $this->event_store = new AdapterFactory\EventSourced\EventStore();
        $this->projector_player = new ProjectorPlayer\ClassName();
        $this->projector_position_ledger = new AdapterFactory\EventSourced\ProjectorPositionLedger();
    }

    public function eventStore(): \Projectionist\Adapter\EventStore
    {
        return $this->event_store;
    }

    public function projectorPlayer(): \Projectionist\Adapter\ProjectorPlayer
    {
        return $this->projector_player;
    }

    public function projectorPositionLedger(): \Projectionist\Adapter\ProjectorPositionLedger
    {
        return $this->projector_position_ledger;
    }
}