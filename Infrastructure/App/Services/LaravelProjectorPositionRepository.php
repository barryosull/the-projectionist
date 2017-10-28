<?php namespace Infrastructure\App\Services;

use App\Services\ProjectorPositionRepository;
use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;

class LaravelProjectorPositionRepository implements ProjectorPositionRepository
{
    private $table;

    public function __construct()
    {
        //$this->table = \DB::connection()->table('player_snapshots');
    }

    public function store(ProjectorPosition $projector_position)
    {
        $row = [
            'class_name' => $projector_position->projector_reference->class_path,
            'player_version' => $projector_position->projector_version,
            'version' => $projector_position->processed_events,
            'last_id' => $projector_position->last_event_id,
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
            ->where('player_version', $projector_reference->currentVersion())
            ->first();

        if (!$row) {
            return null;
        }

        return $this->convertRowToSnapshot($row);
    }

    public function all(): array
    {
        $rows = $this->table->get();

        return array_map(function($row) {
            return $this->convertRowToSnapshot($row);
        }, $rows);
    }

    private function convertRowToSnapshot($row): ProjectorPosition
    {
        return new ProjectorPosition(
            $row['class_name'],
            $row['player_version'],
            $row['version'],
            $row['last_id'],
            $row['occurred_at']
        );
    }
}