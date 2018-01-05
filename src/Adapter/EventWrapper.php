<?php namespace Projectionist\Adapter;

interface EventWrapper
{
    public function id();

    public function wrappedEvent();
}