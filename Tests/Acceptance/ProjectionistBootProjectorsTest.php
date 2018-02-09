<?php namespace ProjectonistTests\Acceptance;

use Projectionist\Adapter\EventStore;
use Projectionist\ConfigFactory;
use Projectionist\Adapter\ProjectorPositionLedger;
use Projectionist\Projectionist;
use Projectionist\ProjectionistFactory;
use Projectionist\Services\ProjectorException;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorPositionCollection;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use Projectionist\ValueObjects\ProjectorStatus;
use ProjectonistTests\Fakes\Projectors\BrokenProjector;
use ProjectonistTests\Fakes\Projectors\RunFromLaunch;
use ProjectonistTests\Fakes\Projectors\RunFromStart;
use ProjectonistTests\Fakes\Projectors\RunOnce;
use ProjectonistTests\Fakes\Services\EventStore\ThingHappened;

class ProjectionistBootProjectorsTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ProjectorPositionLedger $projector_position_repo */
    private $projector_position_repo;

    /** @var Projectionist $booter */
    private $projectionist;

    /** @var array $projectors */
    private $projectors;

    /** @var ProjectionistFactory $projectionist_factory */
    private $projectionist_factory;

    /** @var ProjectorReferenceCollection $projector_refs */
    private $projector_refs;

    /** @var EventStore\InMemory $event_store */
    private $event_store;

    const EVENT_ID_1 = 'a6b3fdda-94f7-4d28-a48e-c46c259d55ae';

    public function setUp()
    {
        $config = (new ConfigFactory\InMemory)->make();
        $this->event_store = $config->eventStore();
        $this->event_store->reset();
        $this->projector_position_repo = $config->projectorPositionLedger();

        $this->projectionist_factory = new ProjectionistFactory($config);
        $this->projectors = [new RunFromLaunch, new RunFromStart, new RunOnce];
        $this->projectionist = $this->projectionist_factory->make($this->projectors);
        $this->projector_refs = ProjectorReferenceCollection::fromProjectors($this->projectors);

        $this->seedEvent(self::EVENT_ID_1);
    }

    private function seedEvent(string $event_id)
    {
        $event = new ThingHappened($event_id);
        $this->event_store->appendEvent($event);
    }

    public function tests_boots_all_projectors_if_none_has_been_stored()
    {
        $this->assertEmpty($this->projector_position_repo->fetchCollection($this->projector_refs));

        $this->projectionist->boot();

        $stored_projector_positions = $this->projector_position_repo->fetchCollection($this->projector_refs);

        $actual = $stored_projector_positions->references();

        $this->assertEquals($this->projector_refs, $actual);
        $this->assertProjectorsAreAtPosition($this->projectors, self::EVENT_ID_1, $stored_projector_positions);
    }

    private function assertProjectorsAreAtPosition(array $projectors, string $expected_position, ProjectorPositionCollection $positions)
    {
        $this->assertCount(count($projectors), $positions);
        $positions->each(function(ProjectorPosition $position) use ($expected_position) {
            $this->assertEquals($expected_position, $position->last_position);
        });
    }

    public function test_boot_does_not_play_events_into_run_from_launch_projectors()
    {
        $this->projectionist->boot();

        $this->assertTrue(RunFromStart::hasProjectedEvent(self::EVENT_ID_1));
        $this->assertTrue(RunOnce::hasProjectedEvent(self::EVENT_ID_1));
        $this->assertFalse(RunFromLaunch::hasProjectedEvent(self::EVENT_ID_1));
    }

    public function test_booting_a_broken_projectors_marks_other_projectors_as_stalled()
    {
        $run_once = new RunOnce;
        $broken = new BrokenProjector;
        $run_from_start = new RunFromStart;

        $projectors = [$run_once, $broken, $run_from_start];
        $projectionist = $this->projectionist_factory->make($projectors);

        $this->assertProjectionistFailsOnBoot($projectionist);

        $refs = ProjectorReferenceCollection::fromProjectors($projectors);
        $stored_projector_positions = $this->projector_position_repo->fetchCollection($refs);

        $run_once_pos = $stored_projector_positions->getByReference( ProjectorReference::makeFromProjector($run_once) );
        $broken_pos = $stored_projector_positions->getByReference( ProjectorReference::makeFromProjector($broken) );
        $run_from_start_pos = $stored_projector_positions->getByReference( ProjectorReference::makeFromProjector($run_from_start) );

        $this->assertProjectorIsBroken($broken_pos);
        $this->assertProjectorIsStalled($run_once_pos);
        $this->assertProjectorIsStalled($run_from_start_pos);
    }

    private function assertProjectorIsBroken(ProjectorPosition $position)
    {
        $this->assertTrue($position->status->is(ProjectorStatus::BROKEN), "Projector ".$position->projector_reference->class_path." should be broken");
    }

    public function assertProjectorIsStalled(ProjectorPosition $position)
    {
        $this->assertTrue($position->status->is(ProjectorStatus::STALLED), "Projector ".$position->projector_reference->class_path." should be stalled");
    }

    private function assertProjectionistFailsOnBoot(Projectionist $projectionist)
    {
        $first_boot_failed = false;
        try {
            $projectionist->boot();
        } catch (ProjectorException $e) {
            $first_boot_failed = true;
        }

        $this->assertTrue($first_boot_failed);
    }

    public function test_booting_fails_if_a_projector_is_broken()
    {
        $projectionist = $this->projectionist_factory->make([new BrokenProjector()]);

        $this->assertProjectionistFailsOnBoot($projectionist);

        $this->expectException(ProjectorException::class);

        $projectionist->boot();
    }
}