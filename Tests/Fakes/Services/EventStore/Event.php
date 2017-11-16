<?php namespace Tests\Fakes\Services\EventStore;

class Event implements \App\Services\EventStore\Event
{
    private $id;
    private $type;

    public function __construct(string $id, string $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    public function id()
    {
        return $this->id;
    }

    private function handlerFunctionName(): string
    {
        $type = $this->type;
        $event_type_snakecase = str_replace(".", "_", $type);
        return 'when_'.$event_type_snakecase;
    }

    public function playIntoProjector($projector)
    {
        $method = $this->handlerFunctionName();
        if (method_exists($projector, $method)) {
            $projector->$method();
        }
    }
}