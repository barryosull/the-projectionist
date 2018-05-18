<?php namespace Projectionist\Domain\Strategy;

use Projectionist\Domain\Services\EventWrapper;
use Projectionist\App\Config;
use Projectionist\Domain\Services\ProjectorException;
use Projectionist\Domain\ValueObjects\ProjectorPosition;
use Projectionist\Domain\ValueObjects\ProjectorPositionCollection;
use Projectionist\Domain\ValueObjects\ProjectorReference;
use Projectionist\Domain\ValueObjects\ProjectorReferenceCollection;

class ProjectorPlayer
{
    private $projectorPositionLedger;
    private $eventLog;
    private $eventHandler;

    public function __construct(Config $adapter)
    {
        $this->projectorPositionLedger = $adapter->projectorPositionLedger();
        $this->eventLog = $adapter->eventLog();
        $this->eventHandler = $adapter->eventHandler();
    }

    public function boot(ProjectorReferenceCollection $projector_references)
    {
        $positions = $this->getProjectorPositions($projector_references);

        $positions = $this->playProjectors($positions);

        if ($this->thereWasAFailure()) {
            $positions = $positions->markUnbrokenAsStalled();
        }

        $this->storeProjectorPositions($positions);

        if ($this->thereWasAFailure()) {
            $this->reportFailure();
        }
    }

    public function play(ProjectorReferenceCollection $projector_references)
    {
        $positions = $this->getProjectorPositions($projector_references)->filterOutFailing();

        $positions = $this->playProjectors($positions);

        $this->storeProjectorPositions($positions);

        if ($this->thereWasAFailure()) {
            $this->reportFailure();
        }
    }

    // TODO: Extract into it's own class/concept
    private function getProjectorPositions(ProjectorReferenceCollection $projector_references): ProjectorPositionCollection
    {
        return new ProjectorPositionCollection(
            array_map(function(ProjectorReference $ref){
                $position = $this->projectorPositionLedger->fetch($ref);

                if ($position) {
                    return $position;
                }

                return ProjectorPosition::makeNewUnplayed($ref);
            }, $projector_references->toArray())
        );
    }

    private function storeProjectorPositions(ProjectorPositionCollection $positions)
    {
        foreach ($positions as $position) {
            $this->projectorPositionLedger->store($position);
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
        $event_stream = $this->eventLog->getStream($last_position);

        while ($event = $event_stream->next()) {
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
        if ($this->thereWasAFailure()) {
            return $projector_position;
        }
        $projector = $projector_position->projector_reference->projector();
        try {
            $this->eventHandler->handle($event->wrappedEvent(), $projector);
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