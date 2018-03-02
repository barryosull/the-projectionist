<?php namespace ProjectonistTests\Unit\Services;

use Projectionist\Adapter\ProjectorPositionLedger;
use Projectionist\Services\ProjectorQueryable;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorPositionCollection;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use Projectionist\ValueObjects\ProjectorStatus;
use ProjectonistTests\Fakes\Projectors\RunFromLaunch;
use ProjectonistTests\Fakes\Projectors\RunFromStart;
use ProjectonistTests\Fakes\Projectors\RunOnce;

class ProjectorQueryableTest extends \PHPUnit\Framework\TestCase
{
    /** Deps */

    /** @var ProjectorPositionLedger */
    private $repo;

    /** Under test */

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

        $references = new ProjectorReferenceCollection([$ref]);

        $this->repo->fetchCollection($references)->willReturn(new ProjectorPositionCollection([]));

        $actual = $this->makeQueryable([$projector])->newOrBrokenProjectors();

        $this->assertEquals($references, $actual);
    }

    public function test_that_stored_projectors_are_not_considered_new()
    {
        $ref_1 = ProjectorReference::makeFromProjector(new RunFromStart);
        $ref_2 = ProjectorReference::makeFromProjector(new RunOnce);
        $ref_3 = ProjectorReference::makeFromProjector(new RunFromLaunch);

        $pos_1 = ProjectorPosition::makeNewUnplayed($ref_1);

        $projectors = [new RunFromStart, new RunOnce, new RunFromLaunch];
        $references = ProjectorReferenceCollection::fromProjectors($projectors);

        $this->repo->fetchCollection($references)->willReturn(new ProjectorPositionCollection([$pos_1]));

        $expected = new ProjectorReferenceCollection([$ref_2, $ref_3]);

        $actual = $this->makeQueryable($projectors)->newOrBrokenProjectors();

        $this->assertEquals($expected, $actual);
    }

    public function test_broken_projectors_are_returned()
    {
        $ref_1 = ProjectorReference::makeFromProjector(new RunFromStart);
        $pos_1 = ProjectorPosition::makeNewUnplayed($ref_1)->broken();

        $references = new ProjectorReferenceCollection([$ref_1]);

        $this->repo->fetchCollection($references)->willReturn(new ProjectorPositionCollection([$pos_1]));

        $expected = new ProjectorReferenceCollection([$ref_1]);

        $actual = $this->makeQueryable([$ref_1->projector()])->newOrBrokenProjectors();

        $this->assertEquals($expected, $actual);
    }

    public function test_projectors_with_a_higher_version_than_stored_are_considered_new()
    {
        $projector = new RunOnce;
        $ref = ProjectorReference::makeFromProjectorWithVersion($projector, 1);
        $ref_higher_version = ProjectorReference::makeFromProjectorWithVersion($projector, 2);

        $processed_events = 2;
        $occurred_at = date('Y-m-d H:i:s');
        $last_event_id = '6c040404-80fd-4a4d-98d6-547344d4873a';
        $pos_1 = new ProjectorPosition($ref, $processed_events, $occurred_at, $last_event_id, ProjectorStatus::broken());

        $references = ProjectorReferenceCollection::fromProjectors([$projector]);

        $this->repo->fetchCollection($references)->willReturn(new ProjectorPositionCollection([$pos_1]));

        $expected = new ProjectorReferenceCollection([$ref_higher_version]);

        $actual = $this->makeQueryable([$projector])->newOrBrokenProjectors();

        $this->assertEquals($expected, $actual);
    }
}