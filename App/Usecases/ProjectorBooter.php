<?php namespace App\Usecases;

use App\Services\ProjectorQueryable;
use App\Services\ProjectorSkipper;
use App\Services\ProjectorsPlayer;
use App\ValueObjects\ProjectorMode;
use App\ValueObjects\ProjectorReferenceCollection;

class ProjectorBooter
{
    private $projector_queryable;
    private $projector_skipper;
    private $projectors_player;

    public function __construct(ProjectorQueryable $projector_queryable, ProjectorSkipper $projector_skipper, ProjectorsPlayer $projectors_player)
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
        $this->projectors_player->play($play_to_now_projectors);
    }
}