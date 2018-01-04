<?php namespace Projectionist;

use Projectionist\Adapter\EventStore\Event;
use Projectionist\Adapter\ProjectorPlayer;
use Projectionist\Services\ProjectorQueryable;
use Projectionist\ValueObjects\ProjectorMode;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use Projectionist\ValueObjects\ProjectorPosition;

class Projectionist
{
    private $projector_queryable;
    private $projector_position_ledger;
    private $event_store;
    private $projector_player;

    private $projector_references;

    public function __construct(AdapterFactory $adapter, ProjectorReferenceCollection $projector_references)
    {
        $this->projector_position_ledger = $adapter->projectorPositionLedger();
        $this->event_store = $adapter->eventStore();
        $this->projector_player = $adapter->projectorPlayer();
        $this->projector_queryable = new ProjectorQueryable($adapter->projectorPositionLedger(), $projector_references);

        $this->projector_references = $projector_references;
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
            $this->playProjector($projector_reference);
        }
    }

    private function playProjector(ProjectorReference $projector_reference)
    {
        $projector_position = $this->projector_position_ledger->fetch($projector_reference);
        if (!$projector_position) {
            $projector_position = ProjectorPosition::makeNewUnplayed($projector_reference);
        }

        if ($projector_position->is_broken) {
            return;
        }

        $event_stream = $this->event_store->getStream($projector_position->last_event_id);

        while ($event = $event_stream->next()) {
            if ($event == null) {
                break;
            }

            $projector_position = self::playEventIntoProjector($this->projector_player, $event, $projector_position, $projector_reference->projector());

            if ($projector_position->is_broken) {
                break;
            }
        }
        $this->projector_position_ledger->store($projector_position);
    }

    public static function playEventIntoProjector(
        ProjectorPlayer $projector_player,
        Event $event,
        ProjectorPosition $projector_position,
        $projector
    ) {
        try {
            $projector_player->play($event, $projector);
            $projector_position = $projector_position->played($event);
        } catch (\Throwable $t) {
            $projector_position = $projector_position->broken();
        }
        return $projector_position;
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