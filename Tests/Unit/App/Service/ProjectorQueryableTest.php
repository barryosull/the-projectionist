<?php namespace Tests\Unit\App\Service;

use App\Services\ProjectorPositionRepository;
use App\Services\ProjectorQueryable;
use App\Services\ProjectorRegisterer;
use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;
use App\ValueObjects\ProjectorReferenceCollection;
use Tests\Projectors\RunFromLaunch;
use Tests\Projectors\RunFromStart;
use Tests\Projectors\RunOnce;

class ProjectorQueryableTest extends \PHPUnit_Framework_TestCase
{
    /** Deps */

    /** @var ProjectorPositionRepository */
    private $repo;
    /** @var ProjectorRegisterer */
    private $registerer;

    /** Under test */

    /** @var ProjectorQueryable */
    private $queryable;

    public function setUp()
    {
        $this->repo = $this->prophesize(ProjectorPositionRepository::class);
        $this->registerer = $this->prophesize(ProjectorRegisterer::class);
        $this->queryable = new ProjectorQueryable(
            $this->repo->reveal(),
            $this->registerer->reveal()
        );
    }

    public function test_registered_but_not_stored_projectors_are_considered_new()
    {
        $ref = new ProjectorReference(RunFromStart::class);
        $this->registerer->all()->willReturn([$ref]);
        $this->repo->all()->willReturn([]);

        $expected = new ProjectorReferenceCollection([$ref]);

        $actual = $this->queryable->newProjectors();

        $this->assertEquals($expected, $actual);
    }

    public function test_that_stored_projectors_are_not_considered_new()
    {
        $ref_1 = new ProjectorReference(RunFromStart::class);
        $pos_1 = ProjectorPosition::make($ref_1);

        $ref_2 = new ProjectorReference(RunOnce::class);
        $ref_3 = new ProjectorReference(RunFromLaunch::class);

        $this->registerer->all()->willReturn([$ref_1, $ref_2, $ref_3]);
        $this->repo->all()->willReturn([$pos_1]);

        $expected = new ProjectorReferenceCollection([$ref_2, $ref_3]);

        $actual = $this->queryable->newProjectors();

        $this->assertEquals($expected, $actual);
    }

    public function test_projectors_with_a_higher_version_than_stored_are_new()
    {

    }
}