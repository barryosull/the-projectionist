<?php namespace App\Services;

use App\ValueObjects\ProjectorReference;

interface ProjectorLoader
{
    public function load(ProjectorReference $projectorId);
}