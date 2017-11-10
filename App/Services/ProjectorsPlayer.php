<?php namespace App\Services;

use App\ValueObjects\ProjectorReference;
use App\ValueObjects\ProjectorReferenceCollection;
use App\ValueObjects\ProjectorPosition;

class ProjectorsPlayer
{
    private $projector_position_repository;
    private $projector_loader;
    private $event_store;
    private $projector_player;

    public function __construct(
        ProjectorPositionRepository $projector_position_repository,
        ProjectorLoader $projector_loader,
        EventStore $event_store,
        ProjectorPlayer $projector_player
    ) {
        $this->projector_position_repository = $projector_position_repository;
        $this->projector_loader = $projector_loader;
        $this->event_store = $event_store;
        $this->projector_player = $projector_player;
    }

    public function play(ProjectorReferenceCollection $projector_references)
    {
        foreach ($projector_references as $projector_reference) {
            $this->playProjector($projector_reference);
        }
    }

    private function playProjector(ProjectorReference $projector_reference)
    {
        $projector_position = $this->projector_position_repository->fetch($projector_reference);
        if (!$projector_position) {
            $projector_position = ProjectorPosition::makeNewUnplayed($projector_reference);
        }

        $projector = $this->projector_loader->load($projector_reference);

        $stream = $this->event_store->getStream($projector_position->last_event_id);

        while ($event = $stream->next()) {
            if ($event == null) {
                break;
            }
            $this->projector_position_repository->store($projector_position);
            $projector_position = $this->projector_player->play($event, $projector, $projector_position);
        }
        $this->projector_position_repository->store($projector_position);
    }
}