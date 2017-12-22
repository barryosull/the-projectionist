<?php namespace App\Services;

interface ProjectorPlayer
{
    public function play(EventStore\Event $event, $projector);
}