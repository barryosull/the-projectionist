<?php namespace ProjectonistTests\Unit\Projectionist\Services;

use Projectionist\Services\ProjectorPositionLedger;
use Projectionist\Services\ProjectorQueryable;
use Projectionist\Services\ProjectorRegisterer;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorPositionCollection;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use ProjectonistTests\Fakes\Projectors\RunFromLaunch;
use ProjectonistTests\Fakes\Projectors\RunFromStart;
use ProjectonistTests\Fakes\Projectors\RunOnce;

class ProjectorQueryableTest extends \PHPUnit_Framework_TestCase
{
    /** Deps */

    /** @var ProjectorPositionLedger */
    private $repo;
    /** @var ProjectorRegisterer */
    private $registerer;

    /** Under test */

    /** @var ProjectorQueryable */
    private $queryable;

    public function setUp()
    {
        $this->repo = $this->prophesize(ProjectorPositionLedger::class);
        $this->registerer = $this->prophesize(ProjectorRegisterer::class);
        $this->queryable = new ProjectorQueryable(
            $this->repo->reveal(),
            $this->registerer->reveal()
        );
    }

    public function test_registered_but_not_stored_projectors_are_considered_new()
    {
        $ref = ProjectorReference::makeFromClass(RunFromStart::class);
        $this->registerer->all()->willReturn(new ProjectorReferenceCollection([$ref]));
        $this->repo->all()->willReturn(new ProjectorPositionCollection([]));

        $expected = new ProjectorReferenceCollection([$ref]);

        $actual = $this->queryable->newProjectors();

        $this->assertEquals($expected, $actual);
    }

    public function test_that_stored_projectors_are_not_considered_new()
    {
        $ref_1 = ProjectorReference::makeFromClass(RunFromStart::class);
        $ref_2 = ProjectorReference::makeFromClass(RunOnce::class);
        $ref_3 = ProjectorReference::makeFromClass(RunFromLaunch::class);

        $pos_1 = ProjectorPosition::makeNewUnplayed($ref_1);

        $this->registerer->all()->willReturn(new ProjectorReferenceCollection([$ref_1, $ref_2, $ref_3]));
        $this->repo->all()->willReturn(new ProjectorPositionCollection([$pos_1]));

        $expected = new ProjectorReferenceCollection([$ref_2, $ref_3]);

        $actual = $this->queryable->newProjectors();

        $this->assertEquals($expected, $actual);
    }

    public function test_projectors_with_a_higher_version_than_stored_are_considered_new()
    {
        $ref = ProjectorReference::make(RunOnce::class, 1);
        $ref_higher_version = ProjectorReference::make(RunOnce::class, 2);

        $processed_events = 2;
        $occurred_at = date('Y-m-d H:i:s');
        $last_event_id = '6c040404-80fd-4a4d-98d6-547344d4873a';
        $pos_1 = new ProjectorPosition($ref, $processed_events, $occurred_at, $last_event_id, false);

        $this->registerer->all()->willReturn(new ProjectorReferenceCollection([$ref_higher_version]));
        $this->repo->all()->willReturn(new ProjectorPositionCollection([$pos_1]));

        $expected = new ProjectorReferenceCollection([$ref_higher_version]);

        $actual = $this->queryable->newProjectors();

        $this->assertEquals($expected, $actual);
    }
}