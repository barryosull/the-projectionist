<?php namespace Projectionist\Adapter\InMemory;

use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorPositionCollection;
use Projectionist\ValueObjects\ProjectorReference;

class ProjectorPositionLedger implements \Projectionist\Services\ProjectorPositionLedger
{
    private $store;

    public function __construct()
    {
        $this->store = [];
    }

    public function reset()
    {
        $this->store = [];
    }

    public function store(ProjectorPosition $projector_position)
    {
        $ref = $projector_position->projector_reference;
        $key = $ref->class_path.'-'.$ref->version;
        $this->store[$key] = $projector_position;
    }

    public function fetch(ProjectorReference $projector_reference)
    {
        $key = $projector_reference->class_path.'-'.$projector_reference->version;
        if (isset($this->store[$key])) {
            return $this->store[$key];
        }
        return null;
    }

    public function all(): ProjectorPositionCollection
    {
        return new ProjectorPositionCollection(array_values($this->store));
    }
}