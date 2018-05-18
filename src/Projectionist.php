<?php namespace Projectionist;

use Projectionist\Services\ProjectorQueryable;
use Projectionist\Strategy\ProjectorPlayer;
use Projectionist\Strategy\ProjectorSkipper;
use Projectionist\ValueObjects\ProjectorMode;
use Projectionist\ValueObjects\ProjectorReferenceCollection;

class Projectionist
{
    private $adapter;
    private $projectorPlayer;
    private $projectorSkipper;

    public function __construct(Config $adapter)
    {
        $this->adapter = $adapter;
        $this->projectorPlayer = new ProjectorPlayer($adapter);
        $this->projectorSkipper = new ProjectorSkipper($adapter);
    }

    public function boot(ProjectorReferenceCollection $projectorRefs)
    {
        $projectorQueryable = $this->makeQueryable($projectorRefs);

        $newProjectors = $projectorQueryable->newOrBrokenProjectors();

        $playToNowProjectors = $newProjectors->exclude(ProjectorMode::RUN_FROM_LAUNCH);
        $this->projectorPlayer->boot($playToNowProjectors);

        $skipToNowProjectors = $newProjectors->extract(ProjectorMode::RUN_FROM_LAUNCH);
        $this->projectorSkipper->skip($skipToNowProjectors);
    }

    public function play(ProjectorReferenceCollection $projectorRefs)
    {
        $projectorQueryable = $this->makeQueryable($projectorRefs);

        $projectors = $projectorQueryable->allProjectors();

        $activeProjectors = $projectors->exclude(ProjectorMode::RUN_ONCE);

        $this->projectorPlayer->play($activeProjectors);
    }

    private function makeQueryable(ProjectorReferenceCollection $projectorRefs): ProjectorQueryable
    {
        return new ProjectorQueryable($this->adapter->projectorPositionLedger(), $projectorRefs);
    }
}