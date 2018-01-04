<?php namespace Projectionist\Strategy;

interface EventHandler
{
    public function handle(\Projectionist\Adapter\Event $event, $projector);
}