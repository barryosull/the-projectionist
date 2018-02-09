<?php namespace Projectionist\ValueObjects;

use Illuminate\Support\Collection;

class ProjectorReferenceCollection extends Collection
{
    public static function fromProjectors(array $projectors): ProjectorReferenceCollection
    {
        return new ProjectorReferenceCollection(array_map(function($projector){
            if (!is_object($projector)) {
                throw new \Exception("One of the projectors is not an object, cannot be used as a projector");
            }
            return ProjectorReference::makeFromProjector($projector);
        }, $projectors));
    }


    public function __construct($items = [])
    {
        parent::__construct($items);

        $references = array_map(function(ProjectorReference $reference){
            return $reference->toString();
        }, $items);

        $unique_references = array_unique($references);

        if (count($unique_references) != count($references)) {
            throw new \Exception("Duplicate projector references, not allowed");
        }
    }

    public function extract(string $mode): ProjectorReferenceCollection
    {
        return $this->filter(function(ProjectorReference $projector_reference) use ($mode) {
            return $projector_reference->mode == $mode;
        })->values();
    }

    public function exclude(string $mode): ProjectorReferenceCollection
    {
        return $this->filter(function(ProjectorReference $projector_reference) use ($mode) {
            return $projector_reference->mode != $mode;
        })->values();
    }

    public function extractNewOrFailedProjectors(ProjectorPositionCollection $projector_positions)
    {
        return $this->filter(function(ProjectorReference $projector_reference) use ($projector_positions){
            $projection_pos = $projector_positions->getByReference($projector_reference);
            if (!$projection_pos) {
                return true;
            }
            return $projection_pos->isFailing();
        })->values();
    }

    public function projectors(): array
    {
        return array_map(function(ProjectorReference $reference){
            return $reference->projector();
        }, $this->toArray());
    }

    public function toStrings(): array
    {
        return array_map(function(ProjectorReference $reference){
            return $reference->toString();
        }, $this->toArray());
    }
}