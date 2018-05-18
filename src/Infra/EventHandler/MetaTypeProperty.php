<?php namespace Projectionist\Infra\EventHandler;

use Projectionist\Domain\Strategy\EventHandler;

class MetaTypeProperty implements EventHandler
{
    public function handle($event, $projector)
    {
        $this->playEvent($event, $projector);
    }

    private function playEvent($event, $projector)
    {
        $method = $this->handlerFunctionName($event->type());
        if (method_exists($projector, $method)) {
            $projector->$method($event->event, $event);
        }
    }

    private function handlerFunctionName(string $type): string
    {
        $eventTypeSnakecase = str_replace(".", "_", $type);
        return 'when_'.$eventTypeSnakecase;
    }
}
