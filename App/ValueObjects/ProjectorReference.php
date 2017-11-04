<?php namespace App\ValueObjects;

class ProjectorReference
{
    public $class_path;

    public function __construct(string $class_path)
    {
        if (!class_exists($class_path)) {
            throw new \Exception("Cannot load class '$class_path'");
        }
        $this->class_path = $class_path;
    }

    public function mode()
    {
        $class = $this->class_path;
        if (defined("$class::MODE")) {
            return $class::MODE;
        }
        return ProjectorMode::RUN_FROM_START;
    }

    public function currentVersion()
    {
        $class = $this->class_path;
        return $class::version();
    }
}