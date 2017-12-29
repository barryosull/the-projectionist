<?php namespace Projectionist\App\Services;

use Projectionist\App\ValueObjects\ProjectorReference;

interface ProjectorLoader
{
    public function load(ProjectorReference $projectorId);
}