<?php namespace Projectionist\ValueObjects;

class ProjectorReference
{
    public $projector;
    public $version;
    public $mode;

    private function __construct($projector, int $version)
    {
        $this->projector = $projector;
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
        if (defined($projector::MODE)) {
            return $projector::MODE;
        }
        return self::DEFAULT_MODE;
    }

    const DEFAULT_VERSION = 1;

    public static function makeFromProjector($projector): ProjectorReference
    {
        $version = self::DEFAULT_VERSION;
        if (defined($projector::VERSION)) {
            $version = $projector::VERSION;
        }
        return new ProjectorReference($projector, $version);
    }

    public static function make(string $projector, int $version): ProjectorReference
    {
        return new ProjectorReference($projector, $version);
    }
}