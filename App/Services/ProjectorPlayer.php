<?php namespace App\Services;

use App\ValueObjects\ProjectorPosition;

class ProjectorPlayer
{
    public function play($event, $projector, ProjectorPosition $projector_position): ProjectorPosition
    {
        $method = $this->getHandlerMethodName($event);
        if (method_exists($projector, $method)) {
            $event_body = $event->schema();
            $projector->$method($event_body, $event);
        }
        return $projector_position->played($event);
    }

    protected function canPlay($projector, $event)
    {
        $method = $this->getHandlerMethodName($event);
        return method_exists($projector, $method);
    }

    protected function getHandlerMethodName($event)
    {
        $event_type_snakecase = str_replace(".", "_", $event->type()->value());
        return 'when_'.$event_type_snakecase;
    }
}
