<?php namespace Projectionist\Adapter;

interface EventStream
{
    /** @return Event */
    public function next();
}