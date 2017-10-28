<?php namespace App\ValueObjects;

use Illuminate\Support\Collection;

class ProjectorReferenceCollection extends Collection
{
    public function extract(string $mode): ProjectorReferenceCollection
    {
        return $this->filter(function($projector_reference) use ($mode) {
            return $projector_reference::MODE == $mode;
        });
    }

    public function exclude(string $mode): ProjectorReferenceCollection
    {
        return $this->filter(function($projector_reference) use ($mode) {
            return $projector_reference::MODE != $mode;
        });
    }
}