<?php namespace App\Usecases;

use App\Services\ProjectorQueryable;
use App\ValueObjects\ProjectorMode;
use App\Services\ProjectorsPlayer;

class ProjectorPlayer
{
    private $projectors_queryable;
    private $projectors_player;

    public function __construct(ProjectorQueryable $projectors_queryable, ProjectorsPlayer $projectors_player)
    {
        $this->projectors_queryable = $projectors_queryable;
        $this->projectors_player = $projectors_player;
    }

    public function play()
    {
        $projectors = $this->projectors_queryable->allProjectors();

        $active_projectors = $projectors->exclude(ProjectorMode::RUN_ONCE);
        $this->projectors_player->play($active_projectors);
    }
}