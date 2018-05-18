<?php namespace Projectionist\Domain\ValueObjects;

use Projectionist\Domain\Services\EventWrapper;

class ProjectorPosition
{
    public $projector_reference;
    public $processed_events;
    public $last_position;
    public $occurred_at;
    public $status;

    public function __construct(
        ProjectorReference $projector_reference,
        int $processed_events,
        string $occurred_at,
        string $last_position,
        ProjectorStatus $status
    )
    {
        $this->projector_reference = $projector_reference;
        $this->processed_events = $processed_events;
        $this->last_position = $last_position;
        $this->occurred_at = $occurred_at;
        $this->status = $status;
    }

    public static function makeNewUnplayed(ProjectorReference $projector_reference): ProjectorPosition
    {
        return new ProjectorPosition(
            $projector_reference,
            0,
            '',
            '',
            ProjectorStatus::new()
        );
    }

    public function played(EventWrapper $event): ProjectorPosition
    {
        $event_count = $this->processed_events + 1;

        return new ProjectorPosition(
            $this->projector_reference,
            $event_count,
            date('Y-m-d H:i:s'),
            $event->id(),
            ProjectorStatus::working()
        );
    }

    public function broken(): ProjectorPosition
    {
        return new ProjectorPosition(
            $this->projector_reference,
            $this->processed_events,
            date('Y-m-d H:i:s'),
            $this->last_position,
            ProjectorStatus::broken()
        );
    }

    public function stalled(): ProjectorPosition
    {
        return new ProjectorPosition(
            $this->projector_reference,
            $this->processed_events,
            date('Y-m-d H:i:s'),
            $this->last_position,
            ProjectorStatus::stalled()
        );
    }

    public function isSame(ProjectorReference $current_projector)
    {
        return $this->projector_reference->equals($current_projector);
    }

    public function isFailing()
    {
        return $this->status->is(ProjectorStatus::BROKEN) || $this->status->is(ProjectorStatus::STALLED);
    }
}