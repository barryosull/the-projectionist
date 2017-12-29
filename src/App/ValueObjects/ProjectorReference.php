<?php namespace Projectionist\App\ValueObjects;

class ProjectorReference
{
    public $class_path;
    public $version;
    public $mode;

    private function __construct(string $class_path, int $version)
    {
        if (!class_exists($class_path)) {
            throw new \Exception("Cannot load class '$class_path'");
        }
        $this->class_path = $class_path;
        $this->version = $version;
        $this->mode = $this->mode($class_path);
    }

    public function equals(ProjectorReference $reference)
    {
        return $this->class_path == $reference->class_path && $this->version == $reference->version;
    }

    public function toString()
    {
        return $this->class_path."-".$this->version;
    }

    const DEFAULT_MODE = ProjectorMode::RUN_FROM_START;

    private function mode($class_path)
    {
        if (defined("$class_path::MODE")) {
            return $class_path::MODE;
        }
        return self::DEFAULT_MODE;
    }

    const DEFAULT_VERSION = 1;

    public static function makeFromClass(string $class_path): ProjectorReference
    {
        $version = self::DEFAULT_VERSION;
        if (defined("$class_path::VERSION")) {
            $version = $class_path::VERSION;
        }
        return new ProjectorReference($class_path, $version);
    }

    public static function make(string $class_path, int $version): ProjectorReference
    {
        return new ProjectorReference($class_path, $version);
    }
}