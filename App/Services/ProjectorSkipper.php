<?php namespace App\Services;

use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;
use App\ValueObjects\ProjectorReferenceCollection;

class ProjectorSkipper
{
    private $projector_position_repository;
    private $event_store;

    public function __construct(
        ProjectorPositionRepository $projector_position_repository,
        EventStore $event_store
    ) {
        $this->projector_position_repository = $projector_position_repository;
        $this->event_store = $event_store;
    }

    public function skipToLastEvent(ProjectorReferenceCollection $projector_references)
    {
        $latest_event = $this->event_store->latestEvent();
        foreach ($projector_references as $projector_reference) {
            $this->skipProjectorToEvent($projector_reference, $latest_event);
        }
    }

    private function skipProjectorToEvent(ProjectorReference $projector_reference, $latest_event)
    {
        $projector_position = $this->projector_position_repository->fetch($projector_reference);
        if (!$projector_position) {
            $projector_position = ProjectorPosition::makeNewUnplayed($projector_reference);
        }
        if ($latest_event) {
            $projector_position = $projector_position->played($latest_event);
        }
        
        $this->projector_position_repository->store($projector_position);
    }
}