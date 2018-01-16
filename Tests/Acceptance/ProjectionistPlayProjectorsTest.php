<?php namespace ProjectonistTests\Acceptance;

use Projectionist\Adapter\EventStore;
use Projectionist\Adapter\ProjectorPositionLedger;
use Projectionist\Config;
use Projectionist\ProjectionistFactory;
use Projectionist\Services\ProjectorException;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorPositionCollection;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use ProjectonistTests\Fakes\Projectors\BrokenProjector;
use ProjectonistTests\Fakes\Projectors\RunFromLaunch;
use ProjectonistTests\Fakes\Projectors\RunFromStart;
use ProjectonistTests\Fakes\Projectors\RunOnce;
use ProjectonistTests\Fakes\Services\EventStore\ThingHappened;

class ProjectionistPlayProjectorsTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectionistFactory */
    private $projectionist_factory;

    /** @var ProjectorPositionLedger */
    private $projector_position_ledger;

    /** @var EventStore\InMemory */
    private $event_store;

    public function setUp()
    {
        $adapter_factory = new Config\InMemory();
        $adapter_factory->projectorPositionLedger()->reset();
        $this->projectionist_factory = new ProjectionistFactory($adapter_factory);
        $this->projector_position_ledger = $adapter_factory->projectorPositionLedger();
        $this->event_store = $adapter_factory->eventStore();
        $this->event_store->reset();
    }

    const EVENT_1_ID = '94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c';
    const EVENT_2_ID = '359e43d8-025e-49ec-a017-3a99c1ce89ba';

    private function seedEvent(string $event_id)
    {
        $event = new ThingHappened($event_id);
        $this->event_store->appendEvent($event);
    }

    public function test_plays_projectors_up_till_the_latest_event()
    {
        $this->seedEvent(self::EVENT_1_ID);
        $projectors = [new RunFromLaunch, new RunFromStart];
        $projectionist = $this->projectionist_factory->make($projectors);

        $projectionist->play();

        $projector_refs = ProjectorReferenceCollection::fromProjectors($projectors);
        $stored_projector_positions = $this->projector_position_ledger->fetchCollection($projector_refs);

        $this->assertProjectorsAreAtPosition($projectors, self::EVENT_1_ID, $stored_projector_positions);
        $this->assertProjectorsHaveProcessedEvent($projectors, self::EVENT_1_ID);
    }

    private function assertProjectorsAreAtPosition(array $projectors, string $expected_position, ProjectorPositionCollection $positions)
    {
        $this->assertCount(count($projectors), $positions);
        $positions->each(function(ProjectorPosition $position) use ($expected_position) {
           $this->assertEquals($expected_position, $position->last_position);
        });
    }

    private function assertProjectorsHaveProcessedEvent(array $projectors, string $event_id)
    {
        foreach ($projectors as $projector) {
            $this->assertTrue($projector::hasProjectedEvent($event_id));
        }
    }

    public function test_does_not_play_run_once_projectors()
    {
        $this->seedEvent(self::EVENT_1_ID);
        $projectors = [new RunFromLaunch, new RunFromStart, new RunOnce()];
        $projectionist = $this->projectionist_factory->make($projectors);

        $projectionist->play();

        $projector_refs = ProjectorReferenceCollection::fromProjectors($projectors);
        $stored_projector_positions = $this->projector_position_ledger->fetchCollection($projector_refs);

        $expected = ProjectorReferenceCollection::fromProjectors([new RunFromLaunch, new RunFromStart]);

        $actual = $stored_projector_positions->references();

        $this->assertEquals($expected, $actual);
    }

    public function test_playing_a_broken_projector_fails_elegantly()
    {
        $this->seedEvent(self::EVENT_1_ID);
        $projectors = [new RunFromLaunch, new RunFromStart, new BrokenProjector()];
        $projectionist = $this->projectionist_factory->make($projectors);

        $this->expectException(ProjectorException::class);

        $projectionist->play();
    }

    public function test_playing_after_a_failure_continues_normally()
    {
        $this->seedEvent(self::EVENT_1_ID);
        $projectors = [new RunFromLaunch, new RunFromStart, new BrokenProjector];
        $projectionist = $this->projectionist_factory->make($projectors);

        $first_play_failed = false;
        try {
            $projectionist->play();
        } catch (\Throwable $e) {
            $first_play_failed = true;
        }

        $this->assertTrue($first_play_failed);

        $this->seedEvent(self::EVENT_2_ID);

        $projectionist->play();

        $expected_projectors = [new RunFromLaunch, new RunFromStart];
        $projector_refs = ProjectorReferenceCollection::fromProjectors($expected_projectors);
        $stored_projector_positions = $this->projector_position_ledger->fetchCollection($projector_refs);

        $this->assertProjectorsAreAtPosition($expected_projectors, self::EVENT_2_ID, $stored_projector_positions);
    }
}