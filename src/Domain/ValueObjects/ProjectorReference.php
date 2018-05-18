<?php namespace Projectionist\Domain\ValueObjects;

class ProjectorReference
{
    public $projector;
    public $class_path;
    public $version;
    public $mode;

    public static function makeFromProjectorWithVersion($projector, int $version): ProjectorReference
    {
        return new ProjectorReference($projector, "", $version);
    }

    public static function makeFromProjector($projector): ProjectorReference
    {
        $version = self::getConst($projector, 'VERSION', self::DEFAULT_VERSION);
        return new ProjectorReference($projector, "", $version);
    }

    public static function makeFromProjectorClassPath(string $class_path): ProjectorReference
    {
        if (class_exists($class_path)) {
            throw new \Exception("Cannot create projector reference, '$class_path' does not exist'");
        }
        $version = self::getConst($class_path, 'VERSION', self::DEFAULT_VERSION);
        return new ProjectorReference(null, $class_path, $version);
    }

    private function __construct($projector, string $class_path, int $version)
    {
        $this->projector = $projector;
        $this->class_path = empty($class_path) ? get_class($projector): $class_path;
        $this->version = $version;
        $this->mode = $this->mode($projector);
    }

    public function equals(ProjectorReference $reference)
    {
        return $this->class_path == $reference->class_path && $this->version == $reference->version;
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

    private static function getConst($projector, string $name, $default)
    {
        $class = is_string($projector)
            ? $projector
            : get_class($projector);

        $name = "$class::$name";
        if (defined($name)) {
            return constant($name);
        }

        return $default;
    }
}