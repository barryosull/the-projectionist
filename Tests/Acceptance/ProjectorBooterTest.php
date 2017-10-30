<?php namespace Tests\Acceptance;

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

    public function setUp()
    {
        $this->booter = App::make(ProjectorBooter::class);
        $event_store = App::make(InMemoryEventStore::class);
        $event_store->setEvents([
            new Event('94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c', 'domain.context.aggregate.event', '2017-01-01 00:00:01', new \stdClass())
        ]);
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
}