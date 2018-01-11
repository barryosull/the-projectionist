<?php namespace Projectionist\Adapter\ProjectorPositionLedger;

use Projectionist\Adapter\ProjectorPositionLedger;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorPositionCollection;
use Projectionist\ValueObjects\ProjectorReference;
use Predis;

// TODO: Write test
class Redis implements ProjectorPositionLedger
{
    private $redis;

    const STORE = 'projector_position_ledger';

    public function __construct(Predis\Client $redis)
    {
        $this->redis = $redis;
    }

    public function store(ProjectorPosition $projector_position)
    {
        $field = $projector_position->projector_reference->toString();
        $value = serialize($projector_position);
        $this->redis->hset(self::STORE, $field, $value);
    }

    /** @return ProjectorPosition */
    public function fetch(ProjectorReference $projector_reference)
    {
        $field = $projector_reference->toString();
        $serialized = $this->redis->hget(self::STORE, $field);
        if (!$serialized) {
            return null;
        }
        return unserialize($serialized);
    }

    public function fetchCollection(): ProjectorPositionCollection
    {
        $positions_serialized = $this->redis->hgetall(self::STORE);

        return new ProjectorPositionCollection(array_map(function($position_serialized){
            return unserialize($position_serialized);
        }, $positions_serialized));
    }
}