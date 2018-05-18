<?php namespace Projectionist\Domain\Services;

use Projectionist\Domain\ValueObjects\ProjectorPosition;
use Projectionist\Domain\ValueObjects\ProjectorReference;
use Projectionist\Domain\ValueObjects\ProjectorPositionCollection;
use Projectionist\Domain\ValueObjects\ProjectorReferenceCollection;

interface ProjectorPositionLedger
{
    public function clear();

    public function store(ProjectorPosition $projectorPosition);

    /** @return ProjectorPosition */
    public function fetch(ProjectorReference $projectorReference);

    public function fetchCollection(ProjectorReferenceCollection $references): ProjectorPositionCollection;
}