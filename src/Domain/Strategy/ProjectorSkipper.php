<?php namespace Projectionist\Domain\Strategy;

use Projectionist\Domain\Services\EventWrapper;
use Projectionist\App\Config;
use Projectionist\Domain\ValueObjects\ProjectorReference;
use Projectionist\Domain\ValueObjects\ProjectorReferenceCollection;
use Projectionist\Domain\ValueObjects\ProjectorPosition;

class ProjectorSkipper
{
    private $projectorPositionLedger;
    private $eventLog;

    public function __construct(Config $adapter)
    {
        $this->projectorPositionLedger = $adapter->projectorPositionLedger();
        $this->eventLog = $adapter->eventLog();
    }

    public function skip(ProjectorReferenceCollection $projectorRefs)
    {
        $latest_event = $this->eventLog->latestEvent();
        if ($latest_event == null) {
            return;
        }
        foreach ($projectorRefs as $projectorRef) {
            $this->skipProjectorToEvent($projectorRef, $latest_event);
        }
    }

    private function skipProjectorToEvent(ProjectorReference $projectorReference, EventWrapper $latest_event)
    {
        $projectorPosition = $this->projectorPositionLedger->fetch($projectorReference);
        if (!$projectorPosition) {
            $projectorPosition = ProjectorPosition::makeNewUnplayed($projectorReference);
        }
        if ($latest_event) {
            $projectorPosition = $projectorPosition->played($latest_event);
        }

        $this->projectorPositionLedger->store($projectorPosition);
    }
}