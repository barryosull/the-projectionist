<?php namespace Projectionist\Strategy\EventHandler;

use Projectionist\Adapter\EventStore;
use Projectionist\Strategy\EventHandler;

class ClassName implements EventHandler
{
    public function handle(\Projectionist\Adapter\EventWrapper $event, $projector)
    {
        $event_content = $event->wrappedEvent();

        $method = $this->handlerFunctionName($this->className($event_content));

        if (method_exists($projector, $method)) {
            $projector->$method($event->wrappedEvent());
        }
    }

    private function className($event)
    {
        $namespaces = explode('\\', get_class($event));
        return last($namespaces);
    }

    private function handlerFunctionName(string $type): string
    {
        return "when".$type;
    }
}