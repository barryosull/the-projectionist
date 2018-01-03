<?php namespace Projectionist\Services;

use Projectionist\ValueObjects\ProjectorReferenceCollection;

class ProjectorQueryable
{
    private $projector_position_repository;
    private $projector_registerer;

    public function __construct(ProjectorPositionLedger $position_ledger, ProjectorRegisterer $projector_registerer)
    {
        $this->projector_position_repository = $position_ledger;
        $this->projector_registerer = $projector_registerer;
    }

    public function newProjectors(): ProjectorReferenceCollection
    {
        $projector_positions = $this->projector_position_repository->all();

        $projector_references = $this->projector_registerer->all();

        return $projector_references->extractNewProjectors($projector_positions);
    }

    public function allProjectors(): ProjectorReferenceCollection
    {
        return $this->projector_registerer->all();
    }
}