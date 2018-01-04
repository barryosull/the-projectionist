<?php namespace Projectionist\Adapter;

use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorPositionCollection;

interface ProjectorPositionLedger
{
    public function store(ProjectorPosition $projector_position);

    /** @return ProjectorPosition */
    public function fetch(ProjectorReference $projector_reference);

    public function all(): ProjectorPositionCollection;
}