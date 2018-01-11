<?php namespace Projectionist\Strategy;

use Projectionist\Adapter\EventWrapper;
use Projectionist\Config;
use Projectionist\Services\ProjectorException;
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

    public function playAll(ProjectorReferenceCollection $projector_references)
    {
        foreach ($projector_references as $projector_reference) {
            $projector_position = $this->projector_position_ledger->fetch($projector_reference);

            if (!$projector_position) {
                $projector_position = ProjectorPosition::makeNewUnplayed($projector_reference);
            }

            $this->playProjector($projector_position);
        }
    }

    public function playUnbroken(ProjectorReferenceCollection $projector_references)
    {
        foreach ($projector_references as $projector_reference) {
            $projector_position = $this->projector_position_ledger->fetch($projector_reference);

            if (!$projector_position) {
                $projector_position = ProjectorPosition::makeNewUnplayed($projector_reference);
            }

            if ($projector_position->isFailing()) {
                return;
            }

            $this->playProjector($projector_position);
        }
    }

    private function playProjector(ProjectorPosition $projector_position)
    {
        $event_stream = $this->event_store->getStream($projector_position->last_event_id);
        $projector = $projector_position->projector_reference->projector();

        while ($event = $event_stream->next()) {
            if ($event == null) {
                break;
            }
            $this->playEventIntoProjector($projector_position, $projector, $event);
        }

        $this->projector_position_ledger->store($projector_position);
    }

    private function playEventIntoProjector(ProjectorPosition $projector_position, $projector, EventWrapper $event): ProjectorPosition
    {
        try {
            $this->event_handler->handle($event->wrappedEvent(), $projector);
            return $projector_position->played($event);
        } catch (\Throwable $t) {
            $projector_position = $projector_position->broken();
            $this->projector_position_ledger->store($projector_position);
            throw new ProjectorException("A projector threw an unexpected failure, marking as broken", $t->getCode(), $t);
        }
    }
}