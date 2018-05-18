<?php namespace Projectionist\Domain\Strategy;

interface EventHandler
{
    public function handle($event, $projector);
}