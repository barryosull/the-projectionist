<?php namespace ProjectonistTests\Acceptance;

use Projectionist\Infra\EventLog;
use Projectionist\Domain\Services\ProjectorPositionLedger;
use Projectionist\ConfigFactory;
use Projectionist\Projectionist;
use Projectionist\Domain\Services\ProjectorException;
use Projectionist\Domain\ValueObjects\ProjectorPosition;
use Projectionist\Domain\ValueObjects\ProjectorPositionCollection;
use Projectionist\Domain\ValueObjects\ProjectorReferenceCollection;
use Projectionist\Domain\ValueObjects\ProjectorStatus;
use ProjectonistTests\Fakes\Projectors\BrokenProjector;
use ProjectonistTests\Fakes\Projectors\RunFromLaunch;
use ProjectonistTests\Fakes\Projectors\RunFromStart;
use ProjectonistTests\Fakes\Projectors\RunOnce;
use ProjectonistTests\Fakes\Services\EventLog\ThingHappened;

class ProjectionistPlayProjectorsTest extends \PHPUnit\Framework\TestCase
{
    /** @var Projectionist */
    private $projectionist;

    /** @var ProjectorPositionLedger */
    private $projector_position_ledger;

    /** @var EventLog\InMemory */
    private $event_log;

    public function setUp()
    {
        $config = (new ConfigFactory\InMemory)->make();
        $config->projectorPositionLedger()->clear();
        $this->projectionist = new Projectionist($config);

        $this->projector_position_ledger = $config->projectorPositionLedger();
        $this->event_log = $config->eventLog();
        $this->event_log->reset();
    }

    const EVENT_1_ID = '94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c';
    const EVENT_2_ID = '359e43d8-025e-49ec-a017-3a99c1ce89ba';

    private function seedEvent(string $event_id)
    {
        $event = new ThingHappened($event_id);
        $this->event_log->appendEvent($event);
    }

    public function test_plays_projectors_up_till_the_latest_event()
    {
        $this->seedEvent(self::EVENT_1_ID);
        $projectors = [new RunFromLaunch, new RunFromStart];
        $projectorRefs = ProjectorReferenceCollection::fromProjectors($projectors);

        $this->projectionist->play($projectorRefs);

        $stored_projector_positions = $this->projector_position_ledger->fetchCollection($projectorRefs);

        $this->assertProjectorsAreAtPosition($projectorRefs, self::EVENT_1_ID, $stored_projector_positions);
    }

    private function assertProjectorsAreAtPosition(ProjectorReferenceCollection $projectorRefs, string $expectedPosition, ProjectorPositionCollection $positions)
    {
        $this->assertCount(count($projectorRefs->projectors()), $positions);
        $this->assertProjectorsHaveProcessedEvent($projectorRefs, $expectedPosition);
        $positions->each(function(ProjectorPosition $position) use ($expectedPosition) {
           $this->assertEquals($expectedPosition, $position->last_position);
           $this->assertEquals(ProjectorStatus::working(), $position->status);
        });
    }

    private function assertProjectorsHaveProcessedEvent(ProjectorReferenceCollection $projectorRefs, string $event_id)
    {
        foreach ($projectorRefs->projectors() as $projector) {
            $this->assertTrue($projector::hasProjectedEvent($event_id));
        }
    }

    public function test_does_not_play_run_once_projectors()
    {
        $this->seedEvent(self::EVENT_1_ID);
        $projectors = [new RunFromLaunch, new RunFromStart, new RunOnce()];
        $projectorRefs = ProjectorReferenceCollection::fromProjectors($projectors);

        $this->projectionist->play($projectorRefs);

        $this->assertFalse(RunOnce::hasProjectedEvent(self::EVENT_1_ID));
    }

    public function test_playing_a_broken_projector_fails_elegantly()
    {
        $this->seedEvent(self::EVENT_1_ID);
        $projectors = [new RunFromLaunch, new RunFromStart, new BrokenProjector()];
        $projectorRefs = ProjectorReferenceCollection::fromProjectors($projectors);

        $this->expectException(ProjectorException::class);

        $this->projectionist->play($projectorRefs);
    }

    public function test_playing_after_a_failure_continues_normally()
    {
        $this->seedEvent(self::EVENT_1_ID);

        $projectors = [new RunFromLaunch, new RunFromStart, new BrokenProjector()];
        $projectorRefs = ProjectorReferenceCollection::fromProjectors($projectors);

        $first_play_failed = false;
        try {
            $this->projectionist->play($projectorRefs);
        } catch (\Throwable $e) {
            $first_play_failed = true;
        }

        $this->assertTrue($first_play_failed);

        $this->seedEvent(self::EVENT_2_ID);

        $this->projectionist->play($projectorRefs);

        $expectedProjectors = [new RunFromLaunch, new RunFromStart];
        $expectedProjectorRefs = ProjectorReferenceCollection::fromProjectors($expectedProjectors);
        $stored_projector_positions = $this->projector_position_ledger->fetchCollection($expectedProjectorRefs);

        $this->assertProjectorsAreAtPosition($expectedProjectorRefs, self::EVENT_2_ID, $stored_projector_positions);
    }
}