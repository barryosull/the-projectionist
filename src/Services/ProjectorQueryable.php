<?php namespace Projectionist\Services;

use Projectionist\Adapter\ProjectorPositionLedger;
use Projectionist\Config;
use Projectionist\ValueObjects\ProjectorReferenceCollection;

class ProjectorQueryable
{
    private $projector_position_repository;
    private $projector_references;

    public function __construct(ProjectorPositionLedger $projector_position_ledger, ProjectorReferenceCollection $projector_references)
    {
        $this->projector_position_repository = $projector_position_ledger;
        $this->projector_references = $projector_references;
    }

    public function newProjectors(): ProjectorReferenceCollection
    {
        $projector_positions = $this->projector_position_repository->all();

        return $this->projector_references->extractNewProjectors($projector_positions);
    }

    public function allProjectors(): ProjectorReferenceCollection
    {
        return $this->projector_references;
    }
}