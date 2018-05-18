<?php namespace Projectionist\Config\EventSourced;

use Projectionist\Domain\ValueObjects\ProjectorPosition;
use Projectionist\Domain\ValueObjects\ProjectorPositionCollection;
use Projectionist\Domain\ValueObjects\ProjectorReference;
use Projectionist\Domain\ValueObjects\ProjectorStatus;

// TODO: Write integration test
class EventSourced implements \Projectionist\Domain\Services\ProjectorPositionLedger
{
    private $table;

    public function __construct()
    {
        $this->table = \DB::table('player_snapshots');
    }

    public function store(ProjectorPosition $projectorPosition)
    {
        $row = [
            'class_name' => $projectorPosition->projector_reference->class_path,
            'player_version' => $projectorPosition->projector_reference->version,
            'version' => $projectorPosition->processed_events,
            'last_id' => $projectorPosition->last_position,
            'status' => $projectorPosition->status,
            'occurred_at' => $projectorPosition->occurred_at
        ];

        $key = [
            'class_name' => $row['class_name'],
            'player_version' => $row['player_version']
        ];

        $this->table->updateOrCreate($key, $row);
    }

    public function fetch(ProjectorReference $projectorReference): ProjectorPosition
    {
        $row = $this->table
            ->where('class_name', $projectorReference)
            ->where('player_version', $projectorReference->version)
            ->first();

        if (!$row) {
            return null;
        }

        return $this->convertRowToPosition($projectorReference, $row);
    }

    public function fetchCollection(): ProjectorPositionCollection
    {
        $rows = $this->table->get();

        return new ProjectorPositionCollection(array_map(function($row) {
            return $this->convertRowToPosition($row);
        }, $rows));
    }

    private function convertRowToPosition(ProjectorReference $projectorReference, array $row): ProjectorPosition
    {
        return new ProjectorPosition(
            $projectorReference,
            $row['version'],
            $row['last_id'],
            $row['occurred_at'],
            new ProjectorStatus($row['status'])
        );
    }
}