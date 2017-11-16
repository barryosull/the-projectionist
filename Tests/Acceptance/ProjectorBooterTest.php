<?php namespace Tests\Acceptance;

use App\Services\ProjectorPositionRepository;
use App\Services\ProjectorRegisterer;
use App\Usecases\ProjectorBooter;
use App\ValueObjects\ProjectorReference;
use App\ValueObjects\ProjectorReferenceCollection;
use Bootstrap\App;
use Tests\Fakes\Projectors\RunFromLaunch;
use Tests\Fakes\Projectors\RunFromStart;
use Tests\Fakes\Projectors\RunOnce;
use Infrastructure\App\Services\InMemory\EventStore;
use Tests\Fakes\Services\EventStore\Event;

class ProjectorBooterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ProjectorPositionRepository $projector_position_repo */
    private $projector_position_repo;

    /** @var ProjectorBooter $booter */
    private $booter;

    public function setUp()
    {
        $this->registerProjectors();
        $this->resetProjectorPositionRepo();
        $this->prepareEventStore();

        $this->booter = App::make(ProjectorBooter::class);
    }

    private function registerProjectors()
    {
        /** @var ProjectorRegisterer $registerer */
        $registerer = App::make(ProjectorRegisterer::class);
        $registerer->register(require "Tests/projectors.php");
    }

    private function resetProjectorPositionRepo()
    {
        $this->projector_position_repo = App::make(ProjectorPositionRepository::class);
        $this->projector_position_repo->reset();
    }

    private function prepareEventStore()
    {
        $event = new Event(
            '94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c',
            'domain.context.aggregate.event'
        );

        $event_store = App::make(EventStore::class);
        $event_store->setEvents([$event]);
    }

    public function tests_boots_all_projectors_if_none_has_been_stored()
    {
        $this->assertEmpty($this->projector_position_repo->all());

        $this->booter->boot();

        $stored_projector_positions = $this->projector_position_repo->all();

        $actual = $stored_projector_positions->references();

        $expected = new ProjectorReferenceCollection([
            ProjectorReference::makeFromClass(RunFromLaunch::class),
            ProjectorReference::makeFromClass(RunFromStart::class),
            ProjectorReference::makeFromClass(RunOnce::class)
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function test_events_are_not_played_into_run_from_launch_projectors()
    {
        $this->booter->boot();

        $this->assertTrue(RunFromStart::hasSeenEvent());
        $this->assertTrue(RunOnce::hasSeenEvent());
        $this->assertFalse(RunFromLaunch::hasSeenEvent());
    }
}