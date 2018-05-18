<?php namespace Projectionist\Infra\ProjectorPositionLedger;

use Projectionist\Domain\Services\ProjectorPositionLedger;
use Projectionist\Domain\ValueObjects\ProjectorPosition;
use Projectionist\Domain\ValueObjects\ProjectorPositionCollection;
use Projectionist\Domain\ValueObjects\ProjectorReference;
use Predis;
use Projectionist\Domain\ValueObjects\ProjectorReferenceCollection;
use function Sodium\crypto_box_seed_keypair;

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

    public function fetchCollection(ProjectorReferenceCollection $references): ProjectorPositionCollection
    {
        $fields = $references->toStrings();

        $positions_serialized = $this->redis->hmget(self::STORE, $fields);

        $positions_serialized = array_filter($positions_serialized);

        return new ProjectorPositionCollection(array_map(function($position_serialized){
            return unserialize($position_serialized);
        }, $positions_serialized));
    }
}