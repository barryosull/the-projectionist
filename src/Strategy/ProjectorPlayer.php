<?php namespace Projectionist\Strategy;

use Projectionist\Config;
use Projectionist\Services\ProjectorException;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorReferenceCollection;

class ProjectorPlayer
{
    private $projector_position_ledger;
    private $event_store;
    private $event_handler;

    public function __construct(Config $adapter)
    {
        $this->projector_position_ledger = $adapter->projectorPositionLedger();
        $this->event_store = $adapter->eventStore();
        $this->event_handler = $adapter->eventHandler();
    }

    public function play(ProjectorReferenceCollection $projector_references)
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
            try {
                $this->event_handler->handle($event->wrappedEvent(), $projector_reference->projector());
                $projector_position = $projector_position->played($event);
            } catch (\Throwable $t) {
                $projector_position = $projector_position->broken();
                $this->projector_position_ledger->store($projector_position);
                throw new ProjectorException("A projector threw an unexpected failure, marking as broken", $t->getCode(), $t);
            }
        }
        $this->projector_position_ledger->store($projector_position);
    }
}