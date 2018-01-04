<?php namespace Projectionist;

use Projectionist\Adapter\EventStore\Event;
use Projectionist\Adapter\ProjectorPlayer;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use Projectionist\ValueObjects\ProjectorPosition;

class Projectionist
{
    private $projector_position_ledger;
    private $event_store;
    private $projector_player;

    public function __construct(AdapterFactory $adapter)
    {
        $this->projector_position_ledger = $adapter->projectorPositionLedger();
        $this->event_store = $adapter->eventStore();
        $this->projector_player = $adapter->projectorPlayer();
    }

    public function playCollection(ProjectorReferenceCollection $projector_references)
    {
        foreach ($projector_references as $projector_reference) {
            $this->playProjector($projector_reference);
        }
    }

    public function playProjector(ProjectorReference $projector_reference)
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

    public function skipToLastEvent(ProjectorReferenceCollection $projector_references)
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