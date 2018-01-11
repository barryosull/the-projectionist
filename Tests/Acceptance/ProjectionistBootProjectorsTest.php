<?php namespace ProjectonistTests\Acceptance;

use Projectionist\Adapter\EventStore;
use Projectionist\Config;
use Projectionist\Adapter\ProjectorPositionLedger;
use Projectionist\Projectionist;
use Projectionist\ProjectionistFactory;
use Projectionist\Services\ProjectorException;
use Projectionist\ValueObjects\ProjectorPosition;
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

    /** @var Config */
    private $adapter_factory;

    /** @var Projectionist $booter */
    private $projectionist;

    private $projectors;

    /** @var ProjectionistFactory $projectionist_factory */
    private $projectionist_factory;

    /** @var ProjectorReferenceCollection $projector_refs */
    private $projector_refs;

    public function setUp()
    {
        $this->adapter_factory = new Config\InMemory();

        $this->projectionist_factory = new ProjectionistFactory($this->adapter_factory);

        $this->projectors = [new RunFromLaunch, new RunFromStart, new RunOnce];

        $this->projectionist = $this->projectionist_factory->make($this->projectors);

        $this->projector_refs = ProjectorReferenceCollection::fromProjectors($this->projectors);

        $this->projector_position_repo = $this->adapter_factory->projectorPositionLedger();

        $this->seedEvents($this->adapter_factory->eventStore());
    }

    private function seedEvents(EventStore $event_store)
    {
        $event = new ThingHappened('94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c');
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


    public function test_booting_fails_if_existing_projector_is_broken()
    {
        $projectionist = $this->projectionist_factory->make([new BrokenProjector()]);

        $this->assertProjectionistFailsOnBoot($projectionist);

        $this->expectException(ProjectorException::class);

        $projectionist->boot();
    }
}