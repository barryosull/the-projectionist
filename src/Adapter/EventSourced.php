<?php namespace Projectionist\Adapter;

use Projectionist\Adapter;
use Projectionist\Services;

class EventSourced implements Adapter
{
    private $event_store;
    private $projector_loader;
    private $projector_player;
    private $projector_position_ledger;

    public function __construct(\Projectionist\Infrastructure\Service\Laravel\ProjectorLoader $projector_loader)
    {
        $this->projector_loader = $projector_loader;
        $this->event_store = new Adapter\EventSourced\EventStore();
        $this->projector_player = new ProjectorPlayer\ClassName();
        $this->projector_position_ledger = new Adapter\EventSourced\ProjectorPositionLedger();
    }

    public function eventStore(): Services\EventStore
    {
        return $this->event_store;
    }

    public function projectorLoader(): Services\ProjectorLoader
    {
        return $this->projector_loader;
    }

    public function projectorPlayer(): Services\ProjectorPlayer
    {
        return $this->projector_player;
    }

    public function projectorPositionLedger(): Services\ProjectorPositionLedger
    {
        return $this->projector_position_ledger;
    }
}