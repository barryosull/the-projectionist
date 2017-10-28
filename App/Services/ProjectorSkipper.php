<?php namespace App\Services;

use App\ValueObjects\ProjectorPosition;

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

    public function skipToLastEvent($workflow_ids)
    {
        $latest_event = $this->event_store->latestEvent();
        foreach ($workflow_ids as $workflow_id) {
            $this->skipProjectorToEvent($workflow_id, $latest_event);
        }
    }

    private function skipProjectorToEvent($projector_reference, $latest_event)
    {
        $projector_position = $this->projector_position_repository->fetch($projector_reference);
        if (!$projector_position) {
            $projector_position = ProjectorPosition::make($projector_reference);
        }

        $projector_position = $projector_position->played($latest_event);

        $this->projector_position_repository->store($projector_position);
    }
}