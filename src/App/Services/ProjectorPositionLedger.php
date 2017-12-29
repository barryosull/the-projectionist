<?php namespace Projectionist\App\Services;

use Projectionist\App\ValueObjects\ProjectorPosition;
use Projectionist\App\ValueObjects\ProjectorReference;
use Projectionist\App\ValueObjects\ProjectorPositionCollection;

interface ProjectorPositionLedger
{
    public function store(ProjectorPosition $projector_position);

    /** @return ProjectorPosition */
    public function fetch(ProjectorReference $projector_reference);

    public function all(): ProjectorPositionCollection;
}