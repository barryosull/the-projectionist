<?php namespace ProjectonistTests\Acceptance;

use Projectionist\Infra\EventLog;
use Projectionist\ConfigFactory;
use Projectionist\Infra\ProjectorPositionLedger;
use Projectionist\Projectionist;
use Projectionist\Domain\Services\ProjectorException;
use Projectionist\Domain\ValueObjects\ProjectorPosition;
use Projectionist\Domain\ValueObjects\ProjectorPositionCollection;
use Projectionist\Domain\ValueObjects\ProjectorReference;
use Projectionist\Domain\ValueObjects\ProjectorReferenceCollection;
use Projectionist\Domain\ValueObjects\ProjectorStatus;
use ProjectonistTests\Fakes\Projectors\BrokenProjector;
use ProjectonistTests\Fakes\Projectors\RunFromLaunch;
use ProjectonistTests\Fakes\Projectors\RunFromStart;
use ProjectonistTests\Fakes\Projectors\RunOnce;
use ProjectonistTests\Fakes\Services\EventLog\ThingHappened;

class ProjectionistBootProjectorsTest extends \PHPUnit\Framework\TestCase
{
    /** @var  \Projectionist\Domain\Services\ProjectorPositionLedger $projectorPositionRepo */
    private $projectorPositionRepo;

    /** @var Projectionist $booter */
    private $projectionist;

    /** @var ProjectorReferenceCollection $projectorRefs */
    private $projectorRefs;

    /** @var EventLog\InMemory $eventLog */
    private $eventLog;

    const EVENT_ID_1 = 'a6b3fdda-94f7-4d28-a48e-c46c259d55ae';

    public function setUp()
    {
        $config = (new ConfigFactory\InMemory)->make();
        $this->eventLog = $config->eventLog();
        $this->eventLog->reset();
        $this->projectorPositionRepo = $config->projectorPositionLedger();
        $this->projectionist = new Projectionist($config);

        $projectors = [new RunFromLaunch, new RunFromStart, new RunOnce];
        $this->projectorRefs = ProjectorReferenceCollection::fromProjectors($projectors);

        $this->seedEvent(self::EVENT_ID_1);
    }

    private function seedEvent(string $event_id)
    {
        $event = new ThingHappened($event_id);
        $this->eventLog->appendEvent($event);
    }

    public function tests_boots_all_projectors_if_none_has_been_stored()
    {
        $this->assertEmpty($this->projectorPositionRepo->fetchCollection($this->projectorRefs));

        $this->projectionist->boot($this->projectorRefs);

        $stored_projector_positions = $this->projectorPositionRepo->fetchCollection($this->projectorRefs);

        $actual = $stored_projector_positions->references();

        $this->assertEquals($this->projectorRefs, $actual);
        $this->assertProjectorsAreAtPosition($this->projectorRefs, self::EVENT_ID_1, $stored_projector_positions);
    }

    private function assertProjectorsAreAtPosition(
        ProjectorReferenceCollection $projectorRefs,
        string $expectedPosition,
        ProjectorPositionCollection $positions
    )
    {
        $this->assertCount(count($projectorRefs->projectors()), $positions);
        $positions->each(function(ProjectorPosition $position) use ($expectedPosition) {
            $this->assertEquals($expectedPosition, $position->last_position); // TODO: Change to getMethod
        });
    }

    public function test_boot_does_not_play_events_into_run_from_launch_projectors()
    {
        $this->projectionist->boot($this->projectorRefs);

        $this->assertTrue(RunFromStart::hasProjectedEvent(self::EVENT_ID_1));
        $this->assertTrue(RunOnce::hasProjectedEvent(self::EVENT_ID_1));
        $this->assertFalse(RunFromLaunch::hasProjectedEvent(self::EVENT_ID_1));
    }

    public function test_booting_a_broken_projectors_marks_other_projectors_as_stalled()
    {
        $runFromStart = new RunFromStart;
        $broken = new BrokenProjector;
        $runOnce = new RunOnce;

        $projectorRefs = ProjectorReferenceCollection::fromProjectors([
            $runFromStart,
            $broken,
            $runOnce
        ]);

        $this->assertProjectionistFailsOnBoot($projectorRefs);

        $stored_projector_positions = $this->projectorPositionRepo->fetchCollection($projectorRefs);

        $runOncePos = $stored_projector_positions->getByReference( ProjectorReference::makeFromProjector($runOnce) );
        $brokenPos = $stored_projector_positions->getByReference( ProjectorReference::makeFromProjector($broken) );
        $runFromStartPos = $stored_projector_positions->getByReference( ProjectorReference::makeFromProjector($runFromStart) );

        $this->assertProjectorIsBroken($brokenPos);
        $this->assertProjectorIsStalled($runOncePos);
        $this->assertProjectorIsStalled($runFromStartPos);
    }

    private function assertProjectorIsBroken(ProjectorPosition $position)
    {
        $this->assertTrue($position->status->is(ProjectorStatus::BROKEN), "Projector ".$position->projector_reference->class_path." should be broken");
    }

    public function assertProjectorIsStalled(ProjectorPosition $position)
    {
        $this->assertTrue($position->status->is(ProjectorStatus::STALLED), "Projector ".$position->projector_reference->class_path." should be stalled");
    }

    private function assertProjectionistFailsOnBoot(ProjectorReferenceCollection $projectorRefs)
    {
        $first_boot_failed = false;
        try {
            $this->projectionist->boot($projectorRefs);
        } catch (ProjectorException $e) {
            $first_boot_failed = true;
        }

        $this->assertTrue($first_boot_failed);
    }

    public function test_booting_fails_if_a_projector_is_broken()
    {
        $projectorRefs = ProjectorReferenceCollection::fromProjectors([new BrokenProjector()]);

        $this->assertProjectionistFailsOnBoot($projectorRefs);

        $this->expectException(ProjectorException::class);

        $this->projectionist->boot($projectorRefs);
    }
}