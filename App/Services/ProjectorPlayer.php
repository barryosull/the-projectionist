<?php namespace App\Services;

use App\ValueObjects\Event;
use App\ValueObjects\ProjectorPosition;

class ProjectorPlayer
{
    public function play(Event $event, $projector, ProjectorPosition $projector_position): ProjectorPosition
    {
        $method = $event->handlerFunctionName();
        if (method_exists($projector, $method)) {
            $projector->$method($event->body, $event);
        }
        return $projector_position->played($event);
    }
}
