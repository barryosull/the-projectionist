<?php namespace Projectionist\ValueObjects;

class ProjectorReference
{
    public $projector;
    public $class_path;
    public $version;
    public $mode;

    private function __construct($projector, int $version)
    {
        $this->projector = $projector;
        $this->class_path = get_class($projector);
        $this->version = $version;
        $this->mode = $this->mode($projector);
    }

    public function equals(ProjectorReference $reference)
    {
        return $this->projector == $reference->projector && $this->version == $reference->version;
    }

    public function toString()
    {
        return get_class($this->projector)."-".$this->version;
    }

    public function projector()
    {
        return $this->projector;
    }

    const DEFAULT_MODE = ProjectorMode::RUN_FROM_START;

    private function mode($projector)
    {
        return self::getConst($projector, 'MODE', self::DEFAULT_MODE);
    }

    const DEFAULT_VERSION = 1;

    public static function makeFromProjector($projector): ProjectorReference
    {
        $version = self::getConst($projector, 'VERSION', self::DEFAULT_VERSION);
        return new ProjectorReference($projector, $version);
    }

    private static function getConst($projector, string $name, $default)
    {
        $class = get_class($projector);
        $name = "$class::$name";
        if (defined($name)) {
            return constant($name);
        }

        return $default;
    }

    public static function make($projector, int $version): ProjectorReference
    {
        return new ProjectorReference($projector, $version);
    }
}