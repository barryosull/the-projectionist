<?php namespace Projectionist\Adapter;

use Projectionist\Adapter;
use Projectionist\Services\EventStore;
use Projectionist\Services\ProjectorLoader;
use Projectionist\Services\ProjectorPlayer;
use Projectionist\Services\ProjectorPositionLedger;

class InMemory implements Adapter
{
    private $event_store;
    private $projector_loader;
    private $projector_player;
    private $projector_position_ledger;

    public function __construct(\Projectionist\Infrastructure\Service\Laravel\ProjectorLoader $projector_loader)
    {
        $this->projector_loader = $projector_loader;
        $this->event_store = new Adapter\InMemory\EventStore();
        $this->projector_player = new ProjectorPlayer\ClassName();
        $this->projector_position_ledger = new Adapter\InMemory\ProjectorPositionLedger();
    }

    public function eventStore(): EventStore
    {
        return $this->event_store;
    }

    public function projectorLoader(): ProjectorLoader
    {
        return $this->projector_loader;
    }

    public function projectorPlayer(): ProjectorPlayer
    {
        return $this->projector_player;
    }

    public function projectorPositionLedger(): ProjectorPositionLedger
    {
        return $this->projector_position_ledger;
    }
}