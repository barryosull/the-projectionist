<?php namespace Projectionist\Domain\Services;

use Projectionist\Domain\Services\ProjectorPositionLedger;
use Projectionist\App\Config;
use Projectionist\Domain\ValueObjects\ProjectorReferenceCollection;

class ProjectorQueryable
{
    private $projectorPositionLedger;
    private $projectorReferences;

    public function __construct(ProjectorPositionLedger $projectorPositionLedger, ProjectorReferenceCollection $projectorReferences)
    {
        $this->projectorPositionLedger = $projectorPositionLedger;
        $this->projectorReferences = $projectorReferences;
    }

    public function newOrBrokenProjectors(): ProjectorReferenceCollection
    {
        $projector_positions = $this->projectorPositionLedger->fetchCollection($this->projectorReferences);

        return $this->projectorReferences->extractNewOrFailedProjectors($projector_positions);
    }

    public function allProjectors(): ProjectorReferenceCollection
    {
        return $this->projectorReferences;
    }
}