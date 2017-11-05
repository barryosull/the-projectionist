<?php namespace App\ValueObjects;

class ProjectorPosition
{
    public $projector_reference;
    public $processed_events;
    public $last_event_id;
    public $occurred_at;

    public function __construct(
        ProjectorReference $projector_reference,
        int $processed_events,
        string $occurred_at,
        string $last_event_id
    )
    {
        $this->projector_reference = $projector_reference;
        $this->processed_events = $processed_events;
        $this->last_event_id = $last_event_id;
        $this->occurred_at = $occurred_at;
    }

    public function played($event): ProjectorPosition
    {
        $event_count = $this->processed_events + 1;

        return new ProjectorPosition(
            $this->projector_reference,
            $event_count,
            $event->id,
            date('Y-m-d H:i:s')
        );
    }

    public static function makeNewUnplayed(ProjectorReference $projector_reference): ProjectorPosition
    {
        return new ProjectorPosition(
            $projector_reference,
            0,
            '',
            ''
        );
    }

    public function isSame(ProjectorReference $current_projector)
    {
        return $this->projector_reference->equals($current_projector);
    }
}