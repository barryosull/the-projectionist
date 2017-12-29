<?php namespace Projectionist\Services;

interface ProjectorPlayer
{
    public function play(EventStore\Event $event, $projector);
}