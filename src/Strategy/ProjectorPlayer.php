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

    private $broken_projector_pos;

    public function boot(ProjectorReferenceCollection $projector_references)
    {
        $this->broken_projector_exception = null;

        $positions = new ProjectorPositionCollection(
            array_map(function(ProjectorReference $ref){
                return $this->getProjectorPosition($ref);
            }, $projector_references->toArray())
        );

        $this->broken_projector_pos = null;

        $positions = $positions->map(function(ProjectorPosition $position) {

            if ($this->broken_projector_exception != null) {
                return $position;
            }

            $position = $this->playProjector($position);

            if ($position->isFailing()) {
                $this->broken_projector_pos = $position;
            }

            return $position;
        });

        if ($this->broken_projector_pos) {
            $positions = $positions->map(function(ProjectorPosition $position){
                if ($position->projector_reference->equals($this->broken_projector_pos->projector_reference))  {
                    return $position;
                }
                return $position->stalled();
            });
        }
        
        foreach ($positions as $position) {
            $this->projector_position_ledger->store($position);
        }


        if ($this->broken_projector_exception != null) {
            throw new ProjectorException(
                "A projector had an unexpected failure",
                $this->broken_projector_exception->getCode(),
                $this->broken_projector_exception
            );
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

    public function play(ProjectorReferenceCollection $projector_references)
    {
        $this->broken_projector_exception = null;

        $positions = new ProjectorPositionCollection(
            array_map(function(ProjectorReference $ref){
                return $this->getProjectorPosition($ref);
            }, $projector_references->toArray())
        );

        $positions = $positions->filterOutFailing();

        $positions = $positions->map(function(ProjectorPosition $position){

            if ($this->broken_projector_exception != null) {
                return $position;
            }

            return $this->playProjector($position);
        });

        foreach ($positions as $position) {
            $this->projector_position_ledger->store($position);
        }

        if ($this->broken_projector_exception != null) {
            throw new ProjectorException(
                "A projector had an unexpected failure",
                $this->broken_projector_exception->getCode(),
                $this->broken_projector_exception
            );
        }
    }

    private function playProjector(ProjectorPosition $projector_position): ProjectorPosition
    {
        $event_stream = $this->event_store->getStream($projector_position->last_position);
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

        return $projector_position;
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