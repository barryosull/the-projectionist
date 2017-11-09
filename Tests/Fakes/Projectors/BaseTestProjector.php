<?php namespace Tests\Fakes\Projectors;

use App\ValueObjects\ProjectorMode;

class BaseTestProjector
{
    const MODE = ProjectorMode::RUN_FROM_START;
    const VERSION = 1;

    public function when_domain_context_aggregate_event()
    {
        static::$has_seen_event = true;
    }

    protected static $has_seen_event = false;

    public static function hasSeenEvent(): bool
    {
        return static::$has_seen_event;
    }

    public static function version()
    {
        return static::VERSION;
    }

    public static function reset()
    {
        return static::$has_seen_event = false;
    }


}