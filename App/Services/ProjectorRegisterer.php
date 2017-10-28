<?php namespace App\Services;

class ProjectorRegisterer
{
    private $projectors = [];

    public function register($projectors)
    {
        $this->projectors = array_unique(array_merge_recursive($this->projectors, $projectors), SORT_REGULAR);
    }

    public function all()
    {
        return $this->projectors;
    }
}