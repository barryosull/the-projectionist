<?php namespace App\Services\EventStore;

interface Event
{
    public function id();

    public function content();
}