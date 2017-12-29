<?php namespace Projectionist\App\Services;

interface ProjectorPlayer
{
    public function play(EventStore\Event $event, $projector);
}