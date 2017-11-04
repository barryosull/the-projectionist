<?php namespace Infrastructure\App\Services;

use App\Services\ProjectorPositionRepository;
use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;
use App\ValueObjects\ProjectorReferenceCollection;

class InMemoryProjectorPositionRepository implements ProjectorPositionRepository
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
        $key = $ref->class_path.'-'.$projector_position->projector_version;
        $this->store[$key] = $projector_position;
    }

    public function fetch(ProjectorReference $projector_reference)
    {
        $key = $projector_reference->class_path.'-'.$projector_reference->currentVersion();
        if (isset($this->store[$key])) {
            return $this->store[$key];
        }
        return null;
    }

    public function all(): array
    {
        return array_values($this->store);
    }
}