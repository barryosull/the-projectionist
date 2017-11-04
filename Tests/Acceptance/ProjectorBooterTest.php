<?php namespace Tests\Acceptance;

use App\Services\ProjectorPositionRepository;
use App\Services\ProjectorRegisterer;
use App\Usecases\ProjectorBooter;
use App\ValueObjects\Event;
use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;
use Bootstrap\App;
use Tests\Fakes\Projectors\RunFromLaunch;
use Tests\Fakes\Projectors\RunFromStart;
use Tests\Fakes\Projectors\RunOnce;
use Infrastructure\App\Services\InMemoryEventStore;

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
            'domain.context.aggregate.event',
            '2017-01-01 00:00:01',
            new \stdClass()
        );

        $event_store = App::make(InMemoryEventStore::class);
        $event_store->setEvents([$event]);
    }

    public function tests_boots_all_projectors_if_none_has_been_stored()
    {
        $this->assertEmpty($this->projector_position_repo->all());

        $this->booter->boot();

        $stored_projector_positions = $this->projector_position_repo->all();

        $actual = array_map(function(ProjectorPosition $pos){
            return $pos->projector_reference;
        }, $stored_projector_positions);

        $expected = [
            new ProjectorReference(RunFromLaunch::class),
            new ProjectorReference(RunFromStart::class),
            new ProjectorReference(RunOnce::class)
        ];

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