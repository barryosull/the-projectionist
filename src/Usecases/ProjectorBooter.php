<?php namespace Projectionist\Usecases;

use Projectionist\Adapter;
use Projectionist\Services\ProjectorQueryable;
use Projectionist\Projectionist;
use Projectionist\Services\ProjectorRegisterer;
use Projectionist\ValueObjects\ProjectorMode;

class ProjectorBooter
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

    public function boot()
    {
        $new_projectors = $this->projector_queryable->newProjectors();

        $skip_to_now_projectors = $new_projectors->extract(ProjectorMode::RUN_FROM_LAUNCH);
        $this->projectionist->skipToLastEvent($skip_to_now_projectors);

        $play_to_now_projectors = $new_projectors->exclude(ProjectorMode::RUN_FROM_LAUNCH);
        $this->projectionist->playCollection($play_to_now_projectors);
    }
}