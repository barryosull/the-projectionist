<?php namespace App\Services;

use Infrastructure\EventSourced\Event\Event;

class EventSourcedProjectorPlayer implements ProjectorPlayer
{
    public function play(EventStore\Event $event, $projector)
    {
        $this->playEvent($event, $projector);
    }

    private function playEvent(Event $event, $projector)
    {
        $method = $this->handlerFunctionName($event->meta->type());
        if (method_exists($projector, $method)) {
            $projector->$method($event->content, $event->meta);
        }
    }

    private function handlerFunctionName($type): string
    {
        $event_type_snakecase = str_replace(".", "_", $type);
        return 'when_'.$event_type_snakecase;
    }
}
