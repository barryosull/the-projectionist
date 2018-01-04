<?php namespace ProjectonistTests\Unit\Projectionist;

use Projectionist\AdapterFactory;
use Projectionist\Services\EventStore;
use Projectionist\AdapterFactory\ProjectorPlayer\ClassName;
use Projectionist\Projectionist;
use Projectionist\Services\ProjectorPlayer;
use Projectionist\Services\ProjectorPositionLedger;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorReference;
use Prophecy\Argument;
use ProjectonistTests\Fakes\Projectors\BrokenProjector;
use ProjectonistTests\Fakes\Services\EventStore\ThingHappened;

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

    public function test_ignores_broken_projectors()
    {
        $player = $this->prophesize(ProjectorPlayer::class);
        $position_ledger = $this->prophesize(ProjectorPositionLedger::class);

        $ref = ProjectorReference::makeFromProjector(new BrokenProjector);
        $position = ProjectorPosition::makeNewUnplayed($ref)->broken();
        $position_ledger->fetch($ref)->willReturn($position);

        $adapter = $this->makeAdapter($player->reveal(), $position_ledger->reveal());

        $projectionist = new Projectionist($adapter);

        $projectionist->playProjector($ref);

        $player->play(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    private function makeAdapter(ProjectorPlayer $player, ProjectorPositionLedger $ledger): AdapterFactory
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