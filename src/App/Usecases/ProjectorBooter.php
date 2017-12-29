<?php namespace Projectionist\App\Usecases;

use Projectionist\App\Services\ProjectorQueryable;
use Projectionist\App\Services\ProjectorSkipper;
use Projectionist\App\Services\Projectionist;
use Projectionist\App\ValueObjects\ProjectorMode;

class ProjectorBooter
{
    private $projector_queryable;
    private $projector_skipper;
    private $projectors_player;

    public function __construct(ProjectorQueryable $projector_queryable, ProjectorSkipper $projector_skipper, Projectionist $projectors_player)
    {
        $this->projector_queryable = $projector_queryable;
        $this->projector_skipper = $projector_skipper;
        $this->projectors_player = $projectors_player;
    }

    public function boot()
    {
        $new_projectors = $this->projector_queryable->newProjectors();

        $skip_to_now_projectors = $new_projectors->extract(ProjectorMode::RUN_FROM_LAUNCH);
        $this->projector_skipper->skipToLastEvent($skip_to_now_projectors);

        $play_to_now_projectors = $new_projectors->exclude(ProjectorMode::RUN_FROM_LAUNCH);
        $this->projectors_player->playCollection($play_to_now_projectors);
    }
}