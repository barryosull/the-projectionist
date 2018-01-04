<?php namespace Projectionist\Adapter\EventHandler;

use Projectionist\Adapter\EventStore\Event;
use Projectionist\Adapter\EventHandler;

class MetaTypeProperty implements EventHandler
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
