<?php namespace Infrastructure\App\Services;

use App\Services\ProjectorLoader;
use App\ValueObjects\ProjectorReference;

class LaravelProjectorLoader implements ProjectorLoader
{
    public function load(ProjectorReference $projector_class)
    {
        return app($projector_class);
    }
}