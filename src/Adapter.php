<?php namespace Projectionist;

use Projectionist\Services\EventStore;
use Projectionist\Services\ProjectorPlayer;
use Projectionist\Services\ProjectorPositionLedger;

interface Adapter
{
    public function eventStore(): EventStore;

    public function projectorPlayer(): ProjectorPlayer;

    public function projectorPositionLedger(): ProjectorPositionLedger;
}