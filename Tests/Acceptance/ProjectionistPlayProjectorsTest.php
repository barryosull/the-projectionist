<?php namespace ProjectonistTests\Acceptance;

use Projectionist\Adapter\EventStore;
use Projectionist\Adapter\ProjectorPositionLedger;
use Projectionist\Config;
use Projectionist\ProjectionistFactory;
use Projectionist\Services\ProjectorException;
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

    public function setUp()
    {
        $adapter_factory = new Config\InMemory();
        $adapter_factory->projectorPositionLedger()->reset();
        $this->projectionist_factory = new ProjectionistFactory($adapter_factory);
        $this->projector_position_ledger = $adapter_factory->projectorPositionLedger();
        $this->seedEvents($adapter_factory->eventStore());
    }

    private function seedEvents(EventStore $event_store)
    {
        $event = new ThingHappened('94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c');
        $event_store->setEvents([$event]);
    }

    public function test_does_not_play_run_once_projectors()
    {
        $projectors = [new RunFromLaunch, new RunFromStart, new RunOnce()];
        $projector_refs = ProjectorReferenceCollection::fromProjectors($projectors);
        $projectionist = $this->projectionist_factory->make($projectors);

        $projectionist->play();

        $stored_projector_positions = $this->projector_position_ledger->fetchCollection($projector_refs);

        $expected = ProjectorReferenceCollection::fromProjectors([new RunFromLaunch, new RunFromStart]);

        $actual = $stored_projector_positions->references();

        $this->assertEquals($expected, $actual);
    }

    public function test_playing_a_broken_projector_fails()
    {
        $projectors = [new RunFromLaunch, new RunFromStart, new BrokenProjector()];

        $projectionist = $this->projectionist_factory->make($projectors);

        $this->expectException(ProjectorException::class);

        $projectionist->play();
    }

    public function test_playing_after_a_failure_continues_normally()
    {
        $projectors = [new RunFromLaunch, new RunFromStart, new BrokenProjector];

        $projectionist = $this->projectionist_factory->make($projectors);

        $first_play_failed = false;
        try {

            $projectionist->play();
        } catch (\Throwable $e) {
            $first_play_failed = true;
        }

        $this->assertTrue($first_play_failed);

        $projectionist->play();
    }
}