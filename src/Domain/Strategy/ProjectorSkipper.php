<?php namespace Projectionist\Domain\Strategy;

use Projectionist\Domain\Services\EventWrapper;
use Projectionist\Config;
use Projectionist\Domain\ValueObjects\ProjectorReference;
use Projectionist\Domain\ValueObjects\ProjectorReferenceCollection;
use Projectionist\Domain\ValueObjects\ProjectorPosition;

class ProjectorSkipper
{
    private $projector_position_ledger;
    private $event_log;

    public function __construct(Config $adapter)
    {
        $this->projector_position_ledger = $adapter->projectorPositionLedger();
        $this->event_log = $adapter->eventLog();
    }

    public function skip(ProjectorReferenceCollection $projector_references)
    {
        $latest_event = $this->event_log->latestEvent();
        if ($latest_event == null) {
            return;
        }
        foreach ($projector_references as $projector_reference) {
            $this->skipProjectorToEvent($projector_reference, $latest_event);
        }
    }

    private function skipProjectorToEvent(ProjectorReference $projector_reference, EventWrapper $latest_event)
    {
        $projector_position = $this->projector_position_ledger->fetch($projector_reference);
        if (!$projector_position) {
            $projector_position = ProjectorPosition::makeNewUnplayed($projector_reference);
        }
        if ($latest_event) {
            $projector_position = $projector_position->played($latest_event);
        }

        $this->projector_position_ledger->store($projector_position);
    }
}