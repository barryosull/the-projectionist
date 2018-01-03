<?php namespace Projectionist\Infrastructure\Service\Laravel;

use Projectionist\ValueObjects\ProjectorReference;
use Illuminate\Container\Container;

class ProjectorLoader implements \Projectionist\Services\ProjectorLoader
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