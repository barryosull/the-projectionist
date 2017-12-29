<?php namespace Projectionist\Services\EventStore;

interface EventStream
{
    /** @return Event */
    public function next();
}