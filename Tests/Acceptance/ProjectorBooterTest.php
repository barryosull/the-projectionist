<?php namespace Tests\Acceptance;

use App\Usecases\ProjectorBooter;
use Bootstrap\App;

class ProjectorBooterTest extends \PHPUnit_Framework_TestCase
{
    public function test_app_loads()
    {
        /** @var ProjectorBooter $booter */
        $booter = App::make(ProjectorBooter::class);
        $booter->boot();
    }

    public function test_boots_only_run_from_launch_projectors()
    {

    }
}