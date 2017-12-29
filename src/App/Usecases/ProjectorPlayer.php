<?php namespace Projectionist\App\Usecases;

use Projectionist\App\Services\ProjectorQueryable;
use Projectionist\App\ValueObjects\ProjectorMode;
use Projectionist\App\Services\Projectionist;

class ProjectorPlayer
{
    private $projectors_queryable;
    private $projectors_player;

    public function __construct(ProjectorQueryable $projectors_queryable, Projectionist $projectors_player)
    {
        $this->projectors_queryable = $projectors_queryable;
        $this->projectors_player = $projectors_player;
    }

    public function play()
    {
        $projectors = $this->projectors_queryable->allProjectors();

        $active_projectors = $projectors->exclude(ProjectorMode::RUN_ONCE);
        $this->projectors_player->playCollection($active_projectors);
    }
}