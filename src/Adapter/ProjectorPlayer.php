<?php namespace Projectionist\Adapter;

interface ProjectorPlayer
{
    public function play(\Projectionist\Adapter\EventStore\Event $event, $projector);
}