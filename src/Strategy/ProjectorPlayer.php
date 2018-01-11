<?php namespace Projectionist\Strategy;

use Projectionist\Adapter\EventWrapper;
use Projectionist\Config;
use Projectionist\Services\ProjectorException;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorReference;
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

    public function boot(ProjectorReferenceCollection $projector_references)
    {
        $broken_projector_ref = null;
        foreach ($projector_references as $projector_reference) {
            $projector_position = $this->getProjectorPosition($projector_reference);

            $this->playProjector($projector_position);

            if ($this->broken_projector_exception != null) {
                $broken_projector_ref = $projector_reference;
                break;
            }
        }
        if ($broken_projector_ref) {
            $this->markProjectorsUnbrokenProjectosAsStalled($projector_references, $broken_projector_ref);
        }
    }

    private function getProjectorPosition(ProjectorReference $projector_reference): ProjectorPosition
    {
        $projector_position = $this->projector_position_ledger->fetch($projector_reference);

        if ($projector_position) {
            return $projector_position;
        }

        return ProjectorPosition::makeNewUnplayed($projector_reference);
    }

    private function markProjectorsUnbrokenProjectosAsStalled(ProjectorReferenceCollection $references, ProjectorReference $broken_ref)
    {
        $unbroken_projector_ref = $references->filter(function(ProjectorReference$reference) use ($broken_ref) {
            return !$broken_ref->equals($reference);
        });

        foreach ($unbroken_projector_ref as $projector_reference) {
            $projector_position = $this->getProjectorPosition($projector_reference);

            $projector_position = $projector_position->stalled();

            $this->projector_position_ledger->store($projector_position);
        }

        throw new ProjectorException(
            "A projector had an unexpected failure, marking unbroken as stalled",
            $this->broken_projector_exception->getCode(),
            $this->broken_projector_exception
        );
    }

    public function play(ProjectorReferenceCollection $projector_references)
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

            if ($this->broken_projector_exception != null) {
                throw new ProjectorException(
                    "A projector had an unexpected failure",
                    $this->broken_projector_exception->getCode(),
                    $this->broken_projector_exception
                );
            }
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
            $projector_position = $this->playEventIntoProjector($projector_position, $projector, $event);

            if ($projector_position->isFailing()) {
                break;
            }
        }

        $this->projector_position_ledger->store($projector_position);
    }

    private $broken_projector_exception;

    private function playEventIntoProjector(ProjectorPosition $projector_position, $projector, EventWrapper $event): ProjectorPosition
    {
        $this->broken_projector_exception  = null;

        try {
            $this->event_handler->handle($event->wrappedEvent(), $projector);
            return $projector_position->played($event);
        } catch (\Throwable $t) {

            $this->broken_projector_exception = $t;
            return $projector_position->broken();
        }
    }
}