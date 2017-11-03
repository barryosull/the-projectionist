<?php namespace App\ValueObjects;

use Illuminate\Support\Collection;

class ProjectorPositionCollection extends Collection
{
    public function hasSameVersion(ProjectorReference $projector_reference): bool
    {
        $position = $this->first(function(ProjectorPosition $position) use ($projector_reference){
            return $position->isSame($projector_reference);
        });

        return $position != null;
    }


}