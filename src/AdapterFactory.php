<?php namespace Projectionist;

use Projectionist\Services\EventStore;
use Projectionist\Services\ProjectorPlayer;
use Projectionist\Services\ProjectorPositionLedger;

interface AdapterFactory
{
    public function eventStore(): EventStore;

    public function projectorPlayer(): ProjectorPlayer;

    public function projectorPositionLedger(): ProjectorPositionLedger;
}