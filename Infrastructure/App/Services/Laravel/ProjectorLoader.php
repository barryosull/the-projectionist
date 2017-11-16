<?php namespace Infrastructure\App\Services\Laravel;

use App\ValueObjects\ProjectorReference;
use Illuminate\Container\Container;

class ProjectorLoader implements \App\Services\ProjectorLoader
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function load(ProjectorReference $projector_class)
    {
        return $this->container->make($projector_class->class_path);
    }
}