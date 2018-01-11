<?php namespace ProjectonistTests\Integration\Projectionist\Service;

use Projectionist\Adapter\ProjectorPositionLedger;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorPositionCollection;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use ProjectonistTests\Fakes\Projectors\RunFromLaunch;
use ProjectonistTests\Fakes\Projectors\RunFromStart;
use ProjectonistTests\Fakes\Projectors\RunOnce;

abstract class ProjectorPositionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectorPositionLedger */
    private $repo;
    private $ref;
    private $position;

    public function setUp()
    {
        $this->repo = $this->makeRepository();
        $this->ref = ProjectorReference::makeFromProjector(new RunFromStart);
        $this->position = ProjectorPosition::makeNewUnplayed($this->ref);
    }

    abstract protected function makeRepository(): ProjectorPositionLedger;

    public function test_fetching_unstored_returns_null()
    {
        $this->assertNull($this->repo->fetch($this->ref));
    }

    public function test_can_fetch()
    {
        $this->assertNull($this->repo->fetch($this->ref));

        $this->repo->store($this->position);

        $actual = $this->repo->fetch($this->ref);

        $this->assertEquals($this->position, $actual);
    }

    private function makePosition($projector)
    {
        return ProjectorPosition::makeNewUnplayed(
            ProjectorReference::makeFromProjector($projector)
        );
    }

    public function test_can_get_all()
    {
        $pos_1 = $this->makePosition(new RunFromStart);
        $pos_2 = $this->makePosition(new RunFromLaunch);
        $pos_3 = $this->makePosition(new RunOnce);

        $this->repo->store($pos_1);
        $this->repo->store($pos_2);
        $this->repo->store($pos_3);

        $references = new ProjectorReferenceCollection([$pos_1->projector_reference, $pos_2->projector_reference, $pos_3->projector_reference]);

        $expected = new ProjectorPositionCollection([$pos_1, $pos_2, $pos_3]);

        $actual = $this->repo->fetchCollection($references);

        $this->assertEquals($expected, $actual);
    }

    public function test_stores_by_reference_and_version()
    {
        $pos_1 = $this->makePosition(new RunFromStart);
        $pos_1_bumped_version = ProjectorPosition::makeNewUnplayed(
            ProjectorReference::makeFromProjectorWithVersion(new RunFromStart, 2)
        );

        $this->repo->store($pos_1);
        $this->repo->store($pos_1_bumped_version);

        $references = new ProjectorReferenceCollection([$pos_1->projector_reference, $pos_1_bumped_version->projector_reference]);

        $expected = new ProjectorPositionCollection([$pos_1, $pos_1_bumped_version]);

        $actual = $this->repo->fetchCollection($references);

        $this->assertEquals($expected, $actual);
    }
}