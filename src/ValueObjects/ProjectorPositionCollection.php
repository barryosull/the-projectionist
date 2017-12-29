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
        $position = $this->first(function(ProjectorPosition $position) use ($projector_reference){
            return $position->projector_reference->equals($projector_reference);
        });

        return $position != null;
    }

    public function references(): ProjectorReferenceCollection
    {
        return new ProjectorReferenceCollection(array_map(function(ProjectorPosition $pos){
            return $pos->projector_reference;
        }, $this->all()));
    }
}