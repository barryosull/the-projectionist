<?php namespace ProjectonistTests\Fakes\Projectors;

use Projectionist\ValueObjects\ProjectorMode;
use ProjectonistTests\Fakes\Services\EventLog\ThingHappened;

class BaseTestProjector
{
    const MODE = ProjectorMode::RUN_FROM_START;
    const VERSION = 1;

    protected static $projected_events = [];

    public function whenThingHappened(ThingHappened $event)
    {
        static::$projected_events[$event->id()] = true;
    }

    public static function hasProjectedEvent(string $event_id): bool
    {
        return isset(static::$projected_events[$event_id]);
    }

    public static function projectedEvents()
    {
        return static::$projected_events;
    }

    public static function reset()
    {
        return static::$projected_events = [];
    }

    public static function version()
    {
        return static::VERSION;
    }
}