<?php namespace Tests\Acceptance;

use App\Services\ProjectorPositionRepository;
use App\Services\ProjectorRegisterer;
use App\Usecases\ProjectorBooter;
use App\ValueObjects\Event;
use App\ValueObjects\ProjectorReference;
use App\ValueObjects\ProjectorReferenceCollection;
use Bootstrap\App;
use Tests\Fakes\Projectors\NewProjector;
use Tests\Fakes\Projectors\RunFromLaunch;
use Tests\Fakes\Projectors\RunFromStart;
use Tests\Fakes\Projectors\RunOnce;
use Infrastructure\App\Services\InMemoryEventStore;

class ProjectorBooterTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectorBooter $booter */
    private $booter;

    /** @var  Event $event */
    private $event;

    public function setUp()
    {
        $this->event = new Event(
            '94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c',
            'domain.context.aggregate.event',
            '2017-01-01 00:00:01',
            new \stdClass()
        );

        $this->resetProjectorPositionRepo();
        $this->prepareEventStore();

        $this->booter = App::make(ProjectorBooter::class);
    }

    private function resetProjectorPositionRepo()
    {
        $projector_position_repo = App::make(ProjectorPositionRepository::class);
        $projector_position_repo->reset();
    }

    private function prepareEventStore()
    {
        $event_store = App::make(InMemoryEventStore::class);
        $event_store->setEvents([$this->event]);
    }

    // TODO: Move this logic elsewhere, doesn't rely belong in acceptance tests
    public function test_can_check_if_projector_got_event()
    {
        $projector_a = new RunFromStart();
        $projector_b= new RunFromLaunch();

        $projector_a->when_domain_context_aggregate_event($this->event->body, $this->event);

        $this->assertTrue($projector_a::hasSeenEvent());
        $this->assertFalse($projector_b::hasSeenEvent());
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
        $this->booter->boot();

        $this->registerNewProjector();

        $expected = new ProjectorReferenceCollection([
            new ProjectorReference(NewProjector::class),
        ]);

        $actual = $this->booter->boot();

        $this->assertEquals($expected->all(), $actual->all());
    }

    private function registerNewProjector()
    {
        /** @var ProjectorRegisterer $registerer */
        $registerer = App::make(ProjectorRegisterer::class);
        $registerer->register([NewProjector::class]);
    }

    public function test_events_are_not_played_into_run_from_launch_projectors()
    {
        $this->booter->boot();

        $this->assertTrue(RunFromStart::hasSeenEvent());
        $this->assertTrue(RunOnce::hasSeenEvent());
        $this->assertFalse(RunFromLaunch::hasSeenEvent());
    }
}