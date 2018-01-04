<?php namespace Projectionist;

use Projectionist\ValueObjects\ProjectorReferenceCollection;

class ProjectionistFactory
{
    private $adapter_factory;

    public function __construct(Config $adapter_factory)
    {
        $this->adapter_factory = $adapter_factory;
    }

    public function make(array $projectors): Projectionist
    {
        $projection_references = ProjectorReferenceCollection::fromProjectors($projectors);
        return new Projectionist($this->adapter_factory, $projection_references);
    }
}