<?php namespace Projectionist\Adapter;

interface EventStream
{
    /** @return EventWrapper */
    public function next();
}