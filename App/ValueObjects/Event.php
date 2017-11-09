<?php namespace App\ValueObjects;

interface Event
{
    public function id();

    public function playIntoProjector($projector);
}