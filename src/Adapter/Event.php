<?php namespace Projectionist\Adapter;

interface Event
{
    public function id();

    public function content();
}