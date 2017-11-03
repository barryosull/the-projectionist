<?php namespace App\Services;

use App\ValueObjects\ProjectorPositionCollection;
use App\ValueObjects\ProjectorReferenceCollection;

class ProjectorQueryable
{
    private $projector_position_repository;
    private $projector_registerer;

    public function __construct(
        ProjectorPositionRepository $projector_position_repository,
        ProjectorRegisterer $projector_registerer
    ) {
        $this->projector_position_repository = $projector_position_repository;
        $this->projector_registerer = $projector_registerer;
    }

    public function newProjectors(): ProjectorReferenceCollection
    {
        $projector_positions = new ProjectorPositionCollection($this->projector_position_repository->all());

        $projector_references = $this->allProjectors();

        return $projector_references->extractNewProjectors($projector_positions);
    }

    public function allProjectors(): ProjectorReferenceCollection
    {
        $list = $this->projector_registerer->all();
        return new ProjectorReferenceCollection($list);
    }
}