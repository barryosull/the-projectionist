<?php namespace ProjectonistTests\Acceptance;

use Projectionist\Adapter;
use Projectionist\Usecases\ProjectorPlayer;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorReferenceCollection;
use ProjectionistTests\Bootstrap\App;
use ProjectonistTests\Fakes\Projectors\RunFromLaunch;
use ProjectonistTests\Fakes\Projectors\RunFromStart;

class ProjectorPlayerTest extends \PHPUnit_Framework_TestCase
{
    public function test_does_not_play_run_once_projectors()
    {
        /** @var ProjectorPlayer $player */
        $player = App::make(ProjectorPlayer::class);

        /** @var \Projectionist\Adapter\InMemory\ProjectorPositionLedger $projector_position_repo */
        $projector_position_repo = App::make(Adapter::class)->projectorPositionLedger();
        $projector_position_repo->reset();

        $player->play();

        $stored_projector_positions = $projector_position_repo->all();

        $actual = $stored_projector_positions->references();

        $expected = new ProjectorReferenceCollection([
            ProjectorReference::makeFromProjector(new RunFromLaunch),
            ProjectorReference::makeFromProjector(new RunFromStart)
        ]);

        $this->assertEquals($expected, $actual);
    }
}