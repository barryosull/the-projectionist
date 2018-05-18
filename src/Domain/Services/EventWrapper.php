<?php namespace Projectionist\Domain\Services;

interface EventWrapper
{
    public function id();

    public function wrappedEvent();
}