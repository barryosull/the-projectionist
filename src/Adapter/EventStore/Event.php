<?php namespace Projectionist\Adapter\EventStore;

interface Event
{
    public function id();

    public function content();
}