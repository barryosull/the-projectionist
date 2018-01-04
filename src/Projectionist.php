<?php namespace Projectionist;

use Projectionist\Adapter\Event;
use Projectionist\Services\ProjectorQueryable;
use Projectionist\Strategy\ProjectorPlayer;
use Projectionist\ValueObjects\ProjectorMode;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use Projectionist\ValueObjects\ProjectorPosition;

class Projectionist
{
    private $projector_queryable;
    private $projector_position_ledger;
    private $event_store;
    private $event_handler;

    private $projector_references;

    private $projector_player;

    public function __construct(Config $adapter, ProjectorReferenceCollection $projector_references)
    {
        $this->projector_position_ledger = $adapter->projectorPositionLedger();
        $this->event_store = $adapter->eventStore();
        $this->event_handler = $adapter->eventHandler();
        $this->projector_queryable = new ProjectorQueryable($adapter->projectorPositionLedger(), $projector_references);

        $this->projector_references = $projector_references;

        $this->projector_player = new ProjectorPlayer($adapter);
    }

    public function boot()
    {
        $new_projectors = $this->projector_queryable->newProjectors();

        $skip_to_now_projectors = $new_projectors->extract(ProjectorMode::RUN_FROM_LAUNCH);
        $this->skipToLastEvent($skip_to_now_projectors);

        $play_to_now_projectors = $new_projectors->exclude(ProjectorMode::RUN_FROM_LAUNCH);
        $this->playCollection($play_to_now_projectors);
    }

    public function play()
    {
        $projectors = $this->projector_queryable->allProjectors();

        $active_projectors = $projectors->exclude(ProjectorMode::RUN_ONCE);
        $this->playCollection($active_projectors);
    }

    private function playCollection(ProjectorReferenceCollection $projector_references)
    {
        foreach ($projector_references as $projector_reference) {
            $this->projector_player->play($projector_reference);
        }
    }

    private function skipToLastEvent(ProjectorReferenceCollection $projector_references)
    {
        if (!$this->event_store->hasEvents()) {
            return;
        }

        $latest_event = $this->event_store->latestEvent();
        foreach ($projector_references as $projector_reference) {
            $this->skipProjectorToEvent($projector_reference, $latest_event);
        }
    }

    private function skipProjectorToEvent(ProjectorReference $projector_reference, Event $latest_event)
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