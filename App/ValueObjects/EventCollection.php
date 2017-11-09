<?php namespace App\ValueObjects;

use Illuminate\Support\Collection;

class EventCollection extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct(array_map(function(Event $event) {
            return $event;
        }, $items));
    }
}