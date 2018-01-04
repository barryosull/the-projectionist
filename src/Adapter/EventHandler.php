<?php namespace Projectionist\Adapter;

interface EventHandler
{
    public function play(\Projectionist\Adapter\EventStore\Event $event, $projector);
}