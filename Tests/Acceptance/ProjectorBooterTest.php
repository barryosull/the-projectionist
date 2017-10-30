<?php namespace Tests\Acceptance;

use App\Services\ProjectorPositionRepository;
use App\Services\ProjectorRegisterer;
use App\Usecases\ProjectorBooter;
use App\ValueObjects\Event;
use App\ValueObjects\ProjectorReference;
use App\ValueObjects\ProjectorReferenceCollection;
use Bootstrap\App;
use App\Projectors\NewProjector;
use App\Projectors\RunFromLaunch;
use App\Projectors\RunFromStart;
use App\Projectors\RunOnce;
use Infrastructure\App\Services\InMemoryEventStore;

class ProjectorBooterTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectorBooter $booter */
    private $booter;

    /** @var  Event $event */
    private $event;

    public function setUp()
    {

        $this->booter = App::make(ProjectorBooter::class);
        $projector_position_repo = App::make(ProjectorPositionRepository::class);
        $projector_position_repo->reset();
        $event_store = App::make(InMemoryEventStore::class);

        $this->event = new Event('94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c', 'domain.context.aggregate.event', '2017-01-01 00:00:01', new \stdClass());
        $event_store->setEvents([$this->event]);
    }

    public function test_can_check_if_projector_got_event()
    {
        $projector_a = new RunFromStart();
        $projector_b= new RunFromLaunch();

        $projector_a->when_domain_context_aggregate_event($this->event->body, $this->event);

        $this->assertTrue($projector_a::hasPlayedEvent());
        $this->assertFalse($projector_b::hasPlayedEvent());
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
        /** @var ProjectorRegisterer $registerer */
        $registerer = App::make(ProjectorRegisterer::class);

        $this->booter->boot();

        $registerer->register([NewProjector::class]);

        $expected = new ProjectorReferenceCollection([
            new ProjectorReference(NewProjector::class),
        ]);

        $actual = $this->booter->boot();

        $this->assertEquals($expected->all(), $actual->all());
    }

    public function test_events_are_not_played_into_run_from_launch_projectors()
    {
        $this->booter->boot();

        $this->assertTrue(RunFromStart::hasPlayedEvent());
        $this->assertTrue(RunOnce::hasPlayedEvent());
        $this->assertFalse(RunFromLaunch::hasPlayedEvent());
    }
}