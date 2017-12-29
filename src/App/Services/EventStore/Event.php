<?php namespace Projectionist\App\Services\EventStore;

interface Event
{
    public function id();

    public function content();
}