<?php namespace Projectionist\Services\EventStore;

interface Event
{
    public function id();

    public function content();
}