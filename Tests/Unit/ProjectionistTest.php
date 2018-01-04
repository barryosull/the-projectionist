<?php namespace ProjectonistTests\Unit\Projectionist;

use Projectionist\AdapterFactory;
use Projectionist\Adapter\EventStore;
use Projectionist\Strategy\EventHandler;
use Projectionist\Strategy\EventHandler\ClassName;
use Projectionist\Adapter\ProjectorPositionLedger;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\Projectionist;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use ProjectonistTests\Fakes\Projectors\BrokenProjector;
use ProjectonistTests\Fakes\Services\EventStore\ThingHappened;
use Prophecy\Argument;

class ProjectionistTest extends \PHPUnit_Framework_TestCase
{
    // TODO: Clean this up, too much messy logic
    public function test_can_handle_broken_projector()
    {
        $player = new ClassName();
        $ref = ProjectorReference::makeFromProjector(new BrokenProjector);
        $projector_position = ProjectorPosition::makeNewUnplayed($ref);
        $projector = new BrokenProjector();
        $event = new AdapterFactory\InMemory\Event(new ThingHappened(''));

        $projector_position = Projectionist::playEventIntoProjector($player, $event, $projector_position, $projector);

        $this->assertTrue($projector_position->is_broken);
    }

    // TODO: Cleanup, turn into actual unit test
    public function test_ignores_broken_projectors()
    {
        $player = $this->prophesize(EventHandler::class);
        $position_ledger = $this->prophesize(ProjectorPositionLedger::class);

        $ref = ProjectorReference::makeFromProjector(new BrokenProjector);
        $position = ProjectorPosition::makeNewUnplayed($ref)->broken();
        $position_ledger->fetch($ref)->willReturn($position);

        $adapter = $this->makeAdapter($player->reveal(), $position_ledger->reveal());

        $projectionist = new Projectionist($adapter, ProjectorReferenceCollection::fromProjectors([new BrokenProjector]));

        $projectionist->play();

        $player->handle(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    private function makeAdapter(EventHandler $player, ProjectorPositionLedger $ledger): AdapterFactory
    {
        $adapter = $this->prophesize(AdapterFactory::class);

        $adapter->projectorPlayer()->willReturn($player);
        $adapter->projectorPositionLedger()->willReturn($ledger);
        $adapter->eventStore()->willReturn(
            $this->prophesize(EventStore::class)->reveal()
        );

        return $adapter->reveal();
    }
}