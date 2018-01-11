<?php namespace ProjectonistTests\Acceptance;

use Projectionist\Config;
use Projectionist\Adapter\ProjectorPositionLedger;
use Projectionist\Projectionist;
use Projectionist\ProjectionistFactory;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use ProjectonistTests\Fakes\Projectors\RunFromLaunch;
use ProjectonistTests\Fakes\Projectors\RunFromStart;
use ProjectonistTests\Fakes\Projectors\RunOnce;
use ProjectonistTests\Fakes\Services\EventStore\ThingHappened;

class ProjectionistBootProjectorsTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ProjectorPositionLedger $projector_position_repo */
    private $projector_position_repo;

    /** @var Config */
    private $adapter_factory;

    /** @var Projectionist $booter */
    private $projectionist;

    private $projectors;

    /** @var ProjectorReferenceCollection $projector_refs */
    private $projector_refs;

    public function setUp()
    {
        $this->adapter_factory = new Config\InMemory();

        $projectionist_factory = new ProjectionistFactory($this->adapter_factory);

        $this->projectors = require "Tests/projectors.php";
        $this->projector_refs = ProjectorReferenceCollection::fromProjectors($this->projectors);

        $this->projectionist = $projectionist_factory->make($this->projectors);

        $this->resetProjectorPositionRepo();
        $this->seedEvents();
    }

    private function resetProjectorPositionRepo()
    {
        $this->projector_position_repo = $this->adapter_factory->projectorPositionLedger();
        $this->projector_position_repo->reset();
    }

    private function seedEvents()
    {
        $event = new ThingHappened('94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c');

        $event_store = $this->adapter_factory->eventStore();
        $event_store->setEvents([$event]);
    }

    public function tests_boots_all_projectors_if_none_has_been_stored()
    {
        $this->assertEmpty($this->projector_position_repo->fetchCollection($this->projector_refs));

        $this->projectionist->boot();

        $stored_projector_positions = $this->projector_position_repo->fetchCollection($this->projector_refs);

        $actual = $stored_projector_positions->references();

        $this->assertEquals($this->projector_refs, $actual);
    }

    public function test_events_are_not_played_into_run_from_launch_projectors()
    {
        $this->projectionist->boot();

        $this->assertTrue(RunFromStart::hasSeenEvent());
        $this->assertTrue(RunOnce::hasSeenEvent());
        $this->assertFalse(RunFromLaunch::hasSeenEvent());
    }
}