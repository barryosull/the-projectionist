<?php namespace Projectionist;

use Projectionist\Adapter\EventStore;
use Projectionist\Strategy\EventHandler;
use Projectionist\Adapter\ProjectorPositionLedger;

interface Config
{
    public function eventStore(): EventStore;

    public function projectorPlayer(): EventHandler;

    public function projectorPositionLedger(): ProjectorPositionLedger;
}