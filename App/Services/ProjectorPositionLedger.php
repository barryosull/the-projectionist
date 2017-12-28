<?php namespace App\Services;

use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;
use App\ValueObjects\ProjectorPositionCollection;

interface ProjectorPositionLedger
{
    public function store(ProjectorPosition $projector_position);

    /** @return ProjectorPosition */
    public function fetch(ProjectorReference $projector_reference);

    public function all(): ProjectorPositionCollection;
}