<?php namespace Infrastructure\App\Services;

use App\Services\ProjectorLoader;
use App\ValueObjects\ProjectorReference;
use Illuminate\Container\Container;

class LaravelProjectorLoader implements ProjectorLoader
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