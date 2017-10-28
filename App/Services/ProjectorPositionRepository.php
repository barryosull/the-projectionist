<?php namespace App\Services;

use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;

interface ProjectorPositionRepository
{
    public function store(ProjectorPosition $projector_position);

    public function fetch(ProjectorReference $projector_reference): ProjectorPosition;

    public function all(): array;
}