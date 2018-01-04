<?php namespace Projectionist;

use Projectionist\Adapter\EventStore;
use Projectionist\Adapter\ProjectorPlayer;
use Projectionist\Adapter\ProjectorPositionLedger;

interface AdapterFactory
{
    public function eventStore(): EventStore;

    public function projectorPlayer(): ProjectorPlayer;

    public function projectorPositionLedger(): ProjectorPositionLedger;
}