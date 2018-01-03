<?php namespace Projectionist;

use Projectionist\Services\EventStore;
use Projectionist\Services\ProjectorLoader;
use Projectionist\Services\ProjectorPlayer;
use Projectionist\Services\ProjectorPositionLedger;

interface Adapter
{
    public function eventStore(): EventStore;

    public function projectorLoader(): ProjectorLoader;

    public function projectorPlayer(): ProjectorPlayer;

    public function projectorPositionLedger(): ProjectorPositionLedger;
}