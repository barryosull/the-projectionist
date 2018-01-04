<?php namespace Projectionist\Strategy;

interface EventHandler
{
    public function play(\Projectionist\Adapter\EventStore\Event $event, $projector);
}