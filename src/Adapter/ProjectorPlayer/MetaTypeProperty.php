<?php namespace Projectionist\Adapter\ProjectorPlayer;

use Projectionist\Services\EventStore\Event;
use Projectionist\Services\ProjectorPlayer;

class MetaTypeProperty implements ProjectorPlayer
{
    public function play(Event $event, $projector)
    {
        $this->playEvent($event, $projector);
    }

    private function playEvent(Event $event, $projector)
    {
        $method = $this->handlerFunctionName($event->content()->type());
        if (method_exists($projector, $method)) {
            $projector->$method($event->content()->event, $event->content());
        }
    }

    private function handlerFunctionName(string $type): string
    {
        $event_type_snakecase = str_replace(".", "_", $type);
        return 'when_'.$event_type_snakecase;
    }
}
