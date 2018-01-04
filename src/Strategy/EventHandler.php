<?php namespace Projectionist\Strategy;

interface EventHandler
{
    public function handle(\Projectionist\Adapter\EventStore\Event $event, $projector);
}