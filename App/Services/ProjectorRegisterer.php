<?php namespace App\Services;

use App\ValueObjects\ProjectorReference;

class ProjectorRegisterer
{
    private static $projectors = [];

    public function register($projectors)
    {
        self::$projectors = array_unique(
            array_merge_recursive(self::$projectors, $projectors),
            SORT_REGULAR
        );
    }

    // TODO: Make it return a reference collection
    public function all(): array
    {
        return array_map(function($projector_class){
            return ProjectorReference::makeFromClass($projector_class);
        }, self::$projectors);
    }
}