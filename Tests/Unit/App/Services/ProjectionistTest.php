<?php namespace Tests\Unit\App\Services;

use App\Services\EventClassProjectorPlayer;
use App\Services\EventStore;
use App\Services\Projectionist;
use App\Services\ProjectorLoader;
use App\Services\ProjectorPlayer;
use App\Services\ProjectorPositionLedger;
use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;
use Prophecy\Argument;
use Tests\Fakes\Projectors\BrokenProjector;
use Tests\Fakes\Services\EventStore\ThingHappened;

class ProjectionistTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_handle_broken_projector()
    {
        $player = new EventClassProjectorPlayer();
        $ref = ProjectorReference::makeFromClass(BrokenProjector::class);
        $projector_position = ProjectorPosition::makeNewUnplayed($ref);
        $projector = new BrokenProjector();
        $event = new ThingHappened('');

        $projector_position = Projectionist::playEventIntoProjector($player, $event, $projector_position, $projector);

        $this->assertTrue($projector_position->is_broken);
    }

    public function test_ignores_broken_projectors()
    {
        $ref = ProjectorReference::makeFromClass(BrokenProjector::class);

        $position_ledger = $this->prophesize(ProjectorPositionLedger::class);

        $player = $this->prophesize(ProjectorPlayer::class);

        $projectionist = new Projectionist(
            $position_ledger->reveal(),
            $this->prophesize(ProjectorLoader::class)->reveal(),
            $this->prophesize(EventStore::class)->reveal(),
            $player->reveal()
        );

        $position = ProjectorPosition::makeNewUnplayed($ref)->broken();
        $position_ledger->fetch($ref)->willReturn($position);

        $projectionist->playProjector($ref);

        $player->play(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}