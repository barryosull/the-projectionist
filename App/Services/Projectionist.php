<?php namespace App\Services;

use App\Services\EventStore\Event;
use App\ValueObjects\ProjectorReference;
use App\ValueObjects\ProjectorReferenceCollection;
use App\ValueObjects\ProjectorPosition;

class Projectionist
{
    private $projector_position_ledger;
    private $projector_loader;
    private $event_store;
    private $projector_player;

    public function __construct(
        ProjectorPositionLedger $projector_position_ledger,
        ProjectorLoader $projector_loader,
        EventStore $event_store,
        ProjectorPlayer $projector_player
    ) {
        $this->projector_position_ledger = $projector_position_ledger;
        $this->projector_loader = $projector_loader;
        $this->event_store = $event_store;
        $this->projector_player = $projector_player;
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
            //return;
        }

        $projector = $this->projector_loader->load($projector_reference);

        $event_stream = $this->event_store->getStream($projector_position->last_event_id);

        while ($event = $event_stream->next()) {
            if ($event == null) {
                break;
            }

            $projector_position = self::playEventIntoProjector($this->projector_player, $event, $projector_position, $projector);

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
}