<?php namespace App\Services;

use App\Services\EventStore\Event;
use App\ValueObjects\ProjectorPosition;

class ProjectorPlayer
{
    public function play(Event $event, $projector, ProjectorPosition $projector_position): ProjectorPosition
    {
        $event->playIntoProjector($projector);
        return $projector_position->played($event);
    }
}
