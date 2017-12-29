<?php namespace Projectionist\Services;

use Projectionist\ValueObjects\ProjectorReference;

interface ProjectorLoader
{
    public function load(ProjectorReference $projectorId);
}