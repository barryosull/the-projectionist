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

    // TODO: Replace all access to mode with the following
    // TODO: Add default mode here
    public function mode()
    {
        $class = $this->class_path;
        return $class::MODE;
    }

    public function currentVersion()
    {
        $class = $this->class_path;
        return $class::version();
    }
}