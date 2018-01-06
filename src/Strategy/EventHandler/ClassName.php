<?php namespace Projectionist\Strategy\EventHandler;

use Projectionist\Strategy\EventHandler;

/**
 *
 *
 */
class ClassName implements EventHandler
{
    public function handle($event, $projector)
    {
        $method = $this->handlerFunctionName($this->className($event));

        if (method_exists($projector, $method)) {
            $projector->$method($event);
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