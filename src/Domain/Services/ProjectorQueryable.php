<?php namespace Projectionist\Domain\Services;

use Projectionist\Domain\Services\ProjectorPositionLedger;
use Projectionist\App\Config;
use Projectionist\Domain\ValueObjects\ProjectorReferenceCollection;

class ProjectorQueryable
{
    private $projector_position_ledger;
    private $projector_references;

    public function __construct(ProjectorPositionLedger $projector_position_ledger, ProjectorReferenceCollection $projector_references)
    {
        $this->projector_position_ledger = $projector_position_ledger;
        $this->projector_references = $projector_references;
    }

    public function newOrBrokenProjectors(): ProjectorReferenceCollection
    {
        $projector_positions = $this->projector_position_ledger->fetchCollection($this->projector_references);

        return $this->projector_references->extractNewOrFailedProjectors($projector_positions);
    }

    public function allProjectors(): ProjectorReferenceCollection
    {
        return $this->projector_references;
    }
}