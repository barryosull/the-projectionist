<?php namespace App\ValueObjects;

use Illuminate\Support\Collection;

// TODO: Write unit tests
class ProjectorReferenceCollection extends Collection
{
    public function extract(string $mode): ProjectorReferenceCollection
    {
        return $this->filter(function(ProjectorReference $projector_reference) use ($mode) {
            return $projector_reference->mode == $mode;
        });
    }

    public function exclude(string $mode): ProjectorReferenceCollection
    {
        return $this->filter(function(ProjectorReference $projector_reference) use ($mode) {
            return $projector_reference->mode != $mode;
        });
    }

    public function extractNewProjectors(ProjectorPositionCollection $projector_positions)
    {
        return $this->filter(function(ProjectorReference $projector_reference) use ($projector_positions){
            return !$projector_positions->hasSameVersion($projector_reference);
        })->values();
    }
}