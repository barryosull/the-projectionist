<?php namespace Projectionist\Domain\Services;

use Projectionist\Domain\Services\EventWrapper;

interface EventStream
{
    /** @return EventWrapper */
    public function next();
}