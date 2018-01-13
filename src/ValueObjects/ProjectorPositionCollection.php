<?php namespace Projectionist\ValueObjects;

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

    public function hasReference(ProjectorReference $projector_reference): bool
    {
        return $this->getByReference($projector_reference) != null;
    }

    /**
     * @param ProjectorReference $projector_reference
     * @return ProjectorPosition
     */
    public function getByReference(ProjectorReference $projector_reference)
    {
         return $this->first(function(ProjectorPosition $position) use ($projector_reference){
            return $position->projector_reference->equals($projector_reference);
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
        return $this->filter(function(ProjectorPosition $projector_position){
            return $projector_position->isFailing() === false;
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
}