<?php namespace App\ValueObjects;

// TODO: Make compatible with snapshot concept
class Event
{
    public $id;
    public $type;
    public $body;
    public $occured_at;

    public function __construct(string $id, string $type, string $occurred_at, \stdClass $body)
    {
        $this->id = $id;
        $this->type = $type;
        $this->occured_at = $occurred_at;
        $this->body = $body;
    }

    public function id()
    {
        return $this->id;
    }

    public function handlerFunctionName(): string
    {
        $event_type_snakecase = str_replace(".", "_", $this->type);
        return 'when_'.$event_type_snakecase;
    }
}