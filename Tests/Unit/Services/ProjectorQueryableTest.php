<?php namespace ProjectonistTests\Unit\Projectionist\Services;

use Projectionist\Adapter\ProjectorPositionLedger;
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

    /** Under test */

    /** @var ProjectorQueryable */
    private $queryable;

    public function setUp()
    {
        $this->repo = $this->prophesize(ProjectorPositionLedger::class);
    }

    private function makeQueryable(array $projectors): ProjectorQueryable
    {
        $references = ProjectorReferenceCollection::fromProjectors($projectors);
        return new ProjectorQueryable($this->repo->reveal(), $references);
    }

    public function test_registered_but_not_stored_projectors_are_considered_new()
    {
        $projector = new RunFromStart;
        $ref = ProjectorReference::makeFromProjector($projector);
        $this->repo->all()->willReturn(new ProjectorPositionCollection([]));

        $expected = new ProjectorReferenceCollection([$ref]);

        $actual = $this->makeQueryable([$projector])->newProjectors();

        $this->assertEquals($expected, $actual);
    }

    public function test_that_stored_projectors_are_not_considered_new()
    {
        $ref_1 = ProjectorReference::makeFromProjector(new RunFromStart);
        $ref_2 = ProjectorReference::makeFromProjector(new RunOnce);
        $ref_3 = ProjectorReference::makeFromProjector(new RunFromLaunch);

        $pos_1 = ProjectorPosition::makeNewUnplayed($ref_1);

        $this->repo->all()->willReturn(new ProjectorPositionCollection([$pos_1]));

        $expected = new ProjectorReferenceCollection([$ref_2, $ref_3]);

        $actual = $this->makeQueryable([new RunFromStart, new RunOnce, new RunFromLaunch])->newProjectors();

        $this->assertEquals($expected, $actual);
    }

    public function test_projectors_with_a_higher_version_than_stored_are_considered_new()
    {
        $projector = new RunOnce;
        $ref = ProjectorReference::make($projector, 1);
        $ref_higher_version = ProjectorReference::make($projector, 2);

        $processed_events = 2;
        $occurred_at = date('Y-m-d H:i:s');
        $last_event_id = '6c040404-80fd-4a4d-98d6-547344d4873a';
        $pos_1 = new ProjectorPosition($ref, $processed_events, $occurred_at, $last_event_id, false);

        $this->repo->all()->willReturn(new ProjectorPositionCollection([$pos_1]));

        $expected = new ProjectorReferenceCollection([$ref_higher_version]);

        $actual = $this->makeQueryable([$projector])->newProjectors();

        $this->assertEquals($expected, $actual);
    }
}