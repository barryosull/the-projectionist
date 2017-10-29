<?php namespace Tests\Acceptance;

use App\Services\ProjectorRegisterer;
use App\Usecases\ProjectorBooter;
use App\ValueObjects\ProjectorReference;
use App\ValueObjects\ProjectorReferenceCollection;
use Bootstrap\App;
use Tests\Projectors\NewProjector;
use Tests\Projectors\RunFromLaunch;
use Tests\Projectors\RunFromStart;
use Tests\Projectors\RunOnce;

class ProjectorBooterTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectorBooter $booter */
    private $booter;

    public function setUp()
    {
        $this->booter = App::make(ProjectorBooter::class);
    }

    public function tests_boots_all_projectors_if_none_has_been_stored()
    {
        $expected = new ProjectorReferenceCollection([
            new ProjectorReference(RunFromLaunch::class),
            new ProjectorReference(RunFromStart::class),
            new ProjectorReference(RunOnce::class)
        ]);

        $actual = $this->booter->boot();

        $this->assertEquals($expected->all(), $actual->all());
    }

    public function test_does_not_boot_existing_projectors()
    {
        /** @var ProjectorRegisterer $registerer */
        $registerer = App::make(ProjectorRegisterer::class);

        $this->booter->boot();

        $registerer->register([NewProjector::class]);

        $expected = new ProjectorReferenceCollection([
            new ProjectorReference(NewProjector::class),
        ]);

        $actual = $this->booter->boot();

        $this->assertEquals($expected->all(), $actual->all());
    }
}