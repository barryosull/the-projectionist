<?php namespace Projectionist\Adapter\EventStore;

interface EventStream
{
    /** @return Event */
    public function next();
}