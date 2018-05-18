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

    public function store(ProjectorPosition $projectorPosition)
    {
        $ref = $projectorPosition->projector_reference;
        $key = $ref->class_path.'-'.$ref->version;
        $this->store[$key] = $projectorPosition;
    }

    public function fetch(ProjectorReference $projectorReference)
    {
        $key = $projectorReference->class_path.'-'.$projectorReference->version;
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