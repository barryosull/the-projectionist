<?php namespace Projectionist\AdapterFactory\EventSourced;

// TODO: Add back real types when this is transitioned to EventSourced
class Snapshot
{
    protected $version;
    protected $occurred_at;

    protected $id;
    protected $type;
    protected $command_id;
    protected $aggregate_id;
    protected $aggregate_type;
    protected $event;

    public function __construct(
        string $id,
        int $version,
        string $occurred_at,
        string $type,
        string $command_id,
        string $aggregate_id,
        string $aggregate_type,
        \stdClass $event
    )
    {
        $this->id = $id;
        $this->type = $type;
        $this->version = $version;
        $this->occurred_at = $occurred_at;
        $this->command_id = $command_id;
        $this->aggregate_id =  $aggregate_id;
        $this->aggregate_type = $aggregate_type;
        $this->event = $event;
    }

    public function id()
    {
        return $this->id;
    }

    public function aggregate_id()
    {
        return $this->aggregate_id;
    }

    public function type()
    {
        return $this->type;
    }

    public function aggregate_type()
    {
        return $this->aggregate_type;
    }

    public function command_id()
    {
        return $this->command_id;
    }

    public function schema()
    {
        return $this->event;
    }

    public function version()
    {
        return $this->version;
    }

    public function occurred_at()
    {
        return $this->occurred_at;
    }
}