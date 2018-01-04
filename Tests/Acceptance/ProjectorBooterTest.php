<?php namespace ProjectonistTests\Acceptance;

use Projectionist\Adapter;
use Projectionist\Services\ProjectorPositionLedger;
use Projectionist\Services\ProjectorRegisterer;
use Projectionist\Usecases\ProjectorBooter;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use ProjectionistTests\Bootstrap\App;
use ProjectonistTests\Fakes\Projectors\RunFromLaunch;
use ProjectonistTests\Fakes\Projectors\RunFromStart;
use ProjectonistTests\Fakes\Projectors\RunOnce;
use Projectionist\Adapter\InMemory\EventStore;
use ProjectonistTests\Fakes\Services\EventStore\ThingHappened;

class ProjectorBooterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ProjectorPositionLedger $projector_position_repo */
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
        $this->projector_position_repo = App::make(Adapter::class)->projectorPositionLedger();
        $this->projector_position_repo->reset();
    }

    private function prepareEventStore()
    {
        $event = new ThingHappened('94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c');

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
            ProjectorReference::makeFromProjector(new RunFromLaunch),
            ProjectorReference::makeFromProjector(new RunFromStart),
            ProjectorReference::makeFromProjector(new RunOnce)
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