<?php namespace Projectionist\Strategy;

interface EventHandler
{
    public function handle(\Projectionist\Adapter\EventWrapper $event, $projector);
}