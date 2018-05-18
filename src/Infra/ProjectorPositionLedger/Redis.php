<?php namespace Projectionist\Infra\ProjectorPositionLedger;

use Projectionist\Domain\Services\ProjectorPositionLedger;
use Projectionist\Domain\ValueObjects\ProjectorPosition;
use Projectionist\Domain\ValueObjects\ProjectorPositionCollection;
use Projectionist\Domain\ValueObjects\ProjectorReference;
use Predis;
use Projectionist\Domain\ValueObjects\ProjectorReferenceCollection;

class Redis implements ProjectorPositionLedger
{
    private $redis;

    const STORE = 'projector_position_ledger';

    public function __construct(Predis\Client $redis)
    {
        $this->redis = $redis;
    }

    public function clear()
    {
        $this->redis->del([self::STORE]);
    }

    public function store(ProjectorPosition $projectorPosition)
    {
        $field = $projectorPosition->projector_reference->toString();
        $value = serialize($projectorPosition);
        $this->redis->hset(self::STORE, $field, $value);
    }

    /** @return ProjectorPosition */
    public function fetch(ProjectorReference $projectorReference)
    {
        $field = $projectorReference->toString();
        $serialized = $this->redis->hget(self::STORE, $field);
        if (!$serialized) {
            return null;
        }
        return unserialize($serialized);
    }

    public function fetchCollection(ProjectorReferenceCollection $references): ProjectorPositionCollection
    {
        $fields = $references->toStrings();

        $positionsSerialized = $this->redis->hmget(self::STORE, $fields);

        $positionsSerialized = array_filter($positionsSerialized);

        return new ProjectorPositionCollection(array_map(function($positionSerialized){
            return unserialize($positionSerialized);
        }, $positionsSerialized));
    }
}