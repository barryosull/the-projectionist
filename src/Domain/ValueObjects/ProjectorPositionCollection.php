<?php namespace Projectionist\Domain\ValueObjects;

use Illuminate\Support\Collection;

class ProjectorPositionCollection extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct($items);

        $references = array_map(function(ProjectorPosition $position){
           return $position->projector_reference->toString();
        }, $items);

        $unique_references = array_unique($references);

        if (count($unique_references) != count($references)) {
            throw new \Exception("Duplicate projector references, not allowed");
        }
    }

    public function hasReference(ProjectorReference $projectorReference): bool
    {
        return $this->getByReference($projectorReference) != null;
    }

    /**
     * @param ProjectorReference $projectorReference
     * @return ProjectorPosition
     */
    public function getByReference(ProjectorReference $projectorReference)
    {
         return $this->first(function(ProjectorPosition $position) use ($projectorReference){
            return $position->projector_reference->equals($projectorReference);
        });
    }

    public function references(): ProjectorReferenceCollection
    {
        return new ProjectorReferenceCollection(array_map(function(ProjectorPosition $pos){
            return $pos->projector_reference;
        }, $this->all()));
    }

    public function filterOutFailing(): ProjectorPositionCollection
    {
        return $this->filter(function(ProjectorPosition $projectorPosition){
            return $projectorPosition->isFailing() === false;
        });
    }

    public function markUnbrokenAsStalled()
    {
        return $this->map(function(ProjectorPosition $position){
            if ($position->isFailing())  {
                return $position;
            }
            return $position->stalled();
        });
    }

    public function groupByLastPosition(): Collection
    {
        $groupByPosition = [];
        foreach ($this as $position) {
            $groupByPosition[$position->last_position][] = $position;
        }

        return (new Collection($groupByPosition))
            ->map(function($groupedPositions){
                return new ProjectorPositionCollection($groupedPositions);
            });
    }
}