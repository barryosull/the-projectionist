<?php namespace Projectionist\Strategy;

use Projectionist\Adapter\EventWrapper;
use Projectionist\Config;
use Projectionist\Services\ProjectorException;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorPositionCollection;
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

    private function getProjectorPositions(ProjectorReferenceCollection $projector_references): ProjectorPositionCollection
    {
        return new ProjectorPositionCollection(
            array_map(function(ProjectorReference $ref){
                $position = $this->projector_position_ledger->fetch($ref);

                if ($position) {
                    return $position;
                }

                return ProjectorPosition::makeNewUnplayed($ref);
            }, $projector_references->toArray())
        );
    }

    public function boot(ProjectorReferenceCollection $projector_references)
    {
        $positions = $this->getProjectorPositions($projector_references);

        $positions = $this->playProjectors($positions);

        if ($this->thereWasAFailure()) {
            $positions = $positions->markUnbrokenAsStalled();
        }

        foreach ($positions as $position) {
            $this->projector_position_ledger->store($position);
        }

        if ($this->thereWasAFailure()) {
            $this->reportFailure();
        }
    }

    public function play(ProjectorReferenceCollection $projector_references)
    {
        $positions = $this->getProjectorPositions($projector_references)->filterOutFailing();

        $positions = $this->playProjectors($positions);

        foreach ($positions as $position) {
            $this->projector_position_ledger->store($position);
        }

        if ($this->thereWasAFailure()) {
            $this->reportFailure();
        }
    }

    private function playProjectors(ProjectorPositionCollection $positions): ProjectorPositionCollection
    {
        $grouped_by_position = $positions->groupByLastPosition();

        $grouped_by_position = $grouped_by_position->map(function($grouped_positions, $last_position){
            if ($this->thereWasAFailure()) {
                return $grouped_positions;
            }
            return $this->playProjectorsFromPosition($grouped_positions, $last_position);
        });

        return new ProjectorPositionCollection($grouped_by_position->flatten()->toArray());
    }

    private function playProjectorsFromPosition(ProjectorPositionCollection $positions, $last_position): ProjectorPositionCollection
    {
        $event_stream = $this->event_store->getStream($last_position);

        while ($event = $event_stream->next()) {
            if ($event == null) {
                break;
            }
            $positions = $positions->map(function(ProjectorPosition $position) use ($event) {
                return $this->playEventIntoProjector($event, $position);
            });

            if ($this->thereWasAFailure()) {
                return $positions;
            }
        }
        return $positions;
    }

    private function playEventIntoProjector(EventWrapper $event, ProjectorPosition $projector_position): ProjectorPosition
    {
        $projector = $projector_position->projector_reference->projector();
        try {
            $this->event_handler->handle($event->wrappedEvent(), $projector);
            return $projector_position->played($event);
        } catch (\Throwable $t) {
            $this->catchFailure($t);
            return $projector_position->broken();
        }
    }

    private $broken_projector_exception;

    private function thereWasAFailure()
    {
        return $this->broken_projector_exception != null;
    }

    private function reportFailure()
    {
        $exception = $this->broken_projector_exception;
        $this->broken_projector_exception = null;
        throw new ProjectorException(
            "A projector had an unexpected failure",
            $exception->getCode(),
            $exception
        );
    }

    private function catchFailure(\Throwable $t)
    {
        $this->broken_projector_exception = $t;
    }
}