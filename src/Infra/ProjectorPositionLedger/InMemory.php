<?php namespace Projectionist\Infra\ProjectorPositionLedger;

use Projectionist\Domain\ValueObjects\ProjectorPosition;
use Projectionist\Domain\ValueObjects\ProjectorPositionCollection;
use Projectionist\Domain\ValueObjects\ProjectorReference;
use Projectionist\Domain\ValueObjects\ProjectorReferenceCollection;

class InMemory implements \Projectionist\Domain\Services\ProjectorPositionLedger
{
    private $store;

    public function __construct()
    {
        $this->store = [];
    }

    public function clear()
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