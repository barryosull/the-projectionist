<?php namespace App\Services\EventStore;

interface EventStream
{
    /** @return Event */
    public function next();
}