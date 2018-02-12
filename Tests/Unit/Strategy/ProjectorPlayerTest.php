<?php namespace ProjectonistTests\Unit\Strategy;

use Projectionist\Adapter\EventStream;
use Projectionist\Adapter\EventWrapper\Identifiable;
use Projectionist\Config;
use Projectionist\Adapter\EventLog;
use Projectionist\Services\ProjectorException;
use Projectionist\Strategy\EventHandler;
use Projectionist\Strategy\EventHandler\ClassName;
use Projectionist\Adapter\ProjectorPositionLedger;
use Projectionist\Strategy\ProjectorPlayer;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorPositionCollection;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use ProjectonistTests\Fakes\Projectors\RunFromStart;
use ProjectonistTests\Fakes\Projectors\BrokenProjector;
use ProjectonistTests\Fakes\Services\EventLog\ThingHappened;
use Prophecy\Argument;

class ProjectorPlayerTest extends \PHPUnit_Framework_TestCase
{
    public function test_broken_projectors_are_marked_as_broken_and_the_error_is_bubbled_up()
    {
        $event_handler = new ClassName();
        $position_ledger = $this->prophesize(ProjectorPositionLedger::class);
        $event = new ThingHappened('');
        $adapter = $this->makeAdapter($event_handler, $position_ledger->reveal(), [$event]);

        // Expectations
        $broken_projector = new BrokenProjector();
        $ref = ProjectorReference::makeFromProjector($broken_projector);
        $projector_position = ProjectorPosition::makeNewUnplayed($ref);

        $position_ledger->fetch($ref)->willReturn(null);
        $position_ledger->store($projector_position->broken())->shouldBeCalled();
        $this->expectException(ProjectorException::class);

        // Run
        $projector_player = new ProjectorPlayer($adapter);
        $projector_refs = ProjectorReferenceCollection::fromProjectors([$broken_projector]);
        $projector_player->play($projector_refs);
    }

    public function test_boot_attempts_to_play_broken_projectors()
    {
        $event_handler = $this->prophesize(EventHandler::class);
        $position_ledger = $this->prophesize(ProjectorPositionLedger::class);

        $projector = new RunFromStart;
        $ref = ProjectorReference::makeFromProjector($projector);
        $position = ProjectorPosition::makeNewUnplayed($ref)->broken();

        $references = ProjectorReferenceCollection::fromProjectors([$projector]);

        $position_ledger->fetch($ref)->willReturn($position);
        $position_ledger->store(Argument::cetera())->shouldBeCalled();
        $position_ledger->fetchCollection($references)->willReturn(new ProjectorPositionCollection([$position]));

        $event = new ThingHappened('');

        $adapter = $this->makeAdapter($event_handler->reveal(), $position_ledger->reveal(), [$event]);

        $projector_player = new ProjectorPlayer($adapter);

        $projector_player->boot($references);

        $event_handler->handle(Argument::cetera())->shouldHaveBeenCalled();
    }

    public function test_play_ignores_broken_projectors()
    {
        $player = $this->prophesize(EventHandler::class);
        $position_ledger = $this->prophesize(ProjectorPositionLedger::class);

        $ref = ProjectorReference::makeFromProjector(new BrokenProjector);
        $position = ProjectorPosition::makeNewUnplayed($ref)->broken();
        $position_ledger->fetch($ref)->willReturn($position);

        $adapter = $this->makeAdapter($player->reveal(), $position_ledger->reveal());

        $projector_player = new ProjectorPlayer($adapter);

        $refs = ProjectorReferenceCollection::fromProjectors([new BrokenProjector]);

        $projector_player->play($refs);

        $player->handle(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    private function makeAdapter(EventHandler $player, ProjectorPositionLedger $ledger, $events=[]): Config
    {
        $adapter = $this->prophesize(Config::class);

        $event_stream = new EventStream\InMemory($events);

        $event_log = $this->prophesize(EventLog::class);
        $event_log->latestEvent()->willReturn(new Identifiable(last($events)));
        $event_log->getStream("")->willReturn($event_stream);

        $adapter->eventHandler()->willReturn($player);
        $adapter->projectorPositionLedger()->willReturn($ledger);
        $adapter->eventLog()->willReturn($event_log);

        return $adapter->reveal();
    }
}