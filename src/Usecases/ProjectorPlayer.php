<?php namespace Projectionist\Usecases;

use Projectionist\Adapter;
use Projectionist\Services\ProjectorQueryable;
use Projectionist\Services\ProjectorRegisterer;
use Projectionist\ValueObjects\ProjectorMode;
use Projectionist\Projectionist;

class ProjectorPlayer
{
    private $projector_queryable;
    private $projectionist;

    public function __construct(Adapter $adapter)
    {
        $this->projector_queryable = new ProjectorQueryable(
            $adapter->projectorPositionLedger(),
            new ProjectorRegisterer()
        );
        $this->projectionist = new Projectionist($adapter);
    }

    public function play()
    {
        $projectors = $this->projector_queryable->allProjectors();

        $active_projectors = $projectors->exclude(ProjectorMode::RUN_ONCE);
        $this->projectionist->playCollection($active_projectors);
    }
}