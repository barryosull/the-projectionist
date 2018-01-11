<?php namespace Projectionist\Config\EventSourced;

use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorPositionCollection;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorStatus;

// TODO: Write integration test
class EventSourced implements \Projectionist\Adapter\ProjectorPositionLedger
{
    private $table;

    public function __construct()
    {
        $this->table = \DB::table('player_snapshots');
    }

    public function store(ProjectorPosition $projector_position)
    {
        $row = [
            'class_name' => $projector_position->projector_reference->class_path,
            'player_version' => $projector_position->projector_reference->version,
            'version' => $projector_position->processed_events,
            'last_id' => $projector_position->last_position,
            'status' => $projector_position->status,
            'occurred_at' => $projector_position->occurred_at
        ];

        $key = [
            'class_name' => $row['class_name'],
            'player_version' => $row['player_version']
        ];

        $this->table->updateOrCreate($key, $row);
    }

    public function fetch(ProjectorReference $projector_reference): ProjectorPosition
    {
        $row = $this->table
            ->where('class_name', $projector_reference)
            ->where('player_version', $projector_reference->version)
            ->first();

        if (!$row) {
            return null;
        }

        return $this->convertRowToPosition($projector_reference, $row);
    }

    public function fetchCollection(): ProjectorPositionCollection
    {
        $rows = $this->table->get();

        return new ProjectorPositionCollection(array_map(function($row) {
            return $this->convertRowToPosition($row);
        }, $rows));
    }

    private function convertRowToPosition(ProjectorReference $projector_reference, array $row): ProjectorPosition
    {
        return new ProjectorPosition(
            $projector_reference,
            $row['version'],
            $row['last_id'],
            $row['occurred_at'],
            new ProjectorStatus($row['status'])
        );
    }
}