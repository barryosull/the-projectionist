<?php namespace Projectionist\Adapter\ProjectorPositionLedger;

use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorPositionCollection;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorReferenceCollection;

class InMemory implements \Projectionist\Adapter\ProjectorPositionLedger
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
        if (!isset($this->store[$key])) {
            return null;
        }
        return $this->store[$key];
    }

    public function fetchCollection(ProjectorReferenceCollection $references): ProjectorPositionCollection
    {
        $positions = array_map(function(ProjectorReference $ref) {
            return $this->fetch($ref);
        }, $references->toArray());

        return new ProjectorPositionCollection(array_filter($positions));
    }
}