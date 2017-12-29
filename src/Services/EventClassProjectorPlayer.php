<?php namespace Projectionist\Services;

// TODO: Move into another location
class EventClassProjectorPlayer implements ProjectorPlayer
{
    public function play(EventStore\Event $event, $projector)
    {
        $event_content = $event->content();

        $method = "when".$this->className($event_content);

        if (method_exists($projector, $method)) {
            $projector->$method($event->content());
        }
    }

    private function className($event)
    {
        $namespaces = explode('\\', get_class($event));
        return last($namespaces);
    }
}