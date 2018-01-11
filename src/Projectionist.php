<?php namespace Projectionist;

use Projectionist\Services\ProjectorQueryable;
use Projectionist\Strategy\ProjectorPlayer;
use Projectionist\Strategy\ProjectorSkipper;
use Projectionist\ValueObjects\ProjectorMode;
use Projectionist\ValueObjects\ProjectorReferenceCollection;

class Projectionist
{
    private $projector_queryable;
    private $projector_player;
    private $projector_skipper;

    private $projector_references;

    public function __construct(Config $adapter, ProjectorReferenceCollection $projector_references)
    {
        $this->projector_queryable = new ProjectorQueryable($adapter->projectorPositionLedger(), $projector_references);
        $this->projector_player = new ProjectorPlayer($adapter);
        $this->projector_skipper = new ProjectorSkipper($adapter);

        $this->projector_references = $projector_references;
    }

    public function boot()
    {
        $new_projectors = $this->projector_queryable->newOrBrokenProjectors();

        $skip_to_now_projectors = $new_projectors->extract(ProjectorMode::RUN_FROM_LAUNCH);
        $this->projector_skipper->skip($skip_to_now_projectors);

        $play_to_now_projectors = $new_projectors->exclude(ProjectorMode::RUN_FROM_LAUNCH);
        $this->projector_player->boot($play_to_now_projectors);
    }

    public function play()
    {
        $projectors = $this->projector_queryable->allProjectors();

        $active_projectors = $projectors->exclude(ProjectorMode::RUN_ONCE);

        $this->projector_player->play($active_projectors);
    }
}