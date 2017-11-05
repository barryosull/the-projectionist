<?php namespace Tests\Integration\App\Service;

use App\Services\ProjectorPositionRepository;
use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;
use Tests\Fakes\Projectors\RunFromLaunch;
use Tests\Fakes\Projectors\RunFromStart;
use Tests\Fakes\Projectors\RunOnce;

abstract class ProjectorPositionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectorPositionRepository */
    private $repo;
    private $ref;
    private $position;

    public function setUp()
    {
        $this->repo = $this->makeRepository();
        $this->ref = ProjectorReference::makeFromClass(RunFromStart::class);
        $this->position = ProjectorPosition::makeNewUnplayed($this->ref);
    }

    abstract protected function makeRepository(): ProjectorPositionRepository;

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

    private function makePosition(string $projector_class)
    {
        return ProjectorPosition::makeNewUnplayed(
            ProjectorReference::makeFromClass($projector_class)
        );
    }

    public function test_can_get_all()
    {
        $pos_1 = $this->makePosition(RunFromStart::class);
        $pos_2 = $this->makePosition(RunFromLaunch::class);
        $pos_3 = $this->makePosition(RunOnce::class);

        $this->repo->store($pos_1);
        $this->repo->store($pos_2);
        $this->repo->store($pos_3);

        $expected = [$pos_1, $pos_2, $pos_3];

        $this->assertEquals($expected, $this->repo->all());
    }

    public function test_stores_by_reference_and_version()
    {
        $pos_1 = $this->makePosition(RunFromStart::class);
        $pos_1_bumped_version = ProjectorPosition::makeNewUnplayed(
            ProjectorReference::make(RunFromStart::class, 2)
        );

        $this->repo->store($pos_1);
        $this->repo->store($pos_1_bumped_version);

        $expected = [$pos_1, $pos_1_bumped_version];

        $this->assertEquals($expected, $this->repo->all());
    }
}