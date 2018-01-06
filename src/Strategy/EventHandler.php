<?php namespace Projectionist\Strategy;

interface EventHandler
{
    public function handle($event, $projector);
}