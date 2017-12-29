<?php namespace Tests\Acceptance;

use Projectionist\App\Services\ProjectorPositionLedger;
use Projectionist\App\Usecases\ProjectorPlayer;
use Projectionist\App\ValueObjects\ProjectorReference;
use Projectionist\App\ValueObjects\ProjectorReferenceCollection;
use Projectionist\Bootstrap\App;
use Projectionist\Infrastructure\App\Services\InMemory;
use Tests\Fakes\Projectors\RunFromLaunch;
use Tests\Fakes\Projectors\RunFromStart;

class ProjectorPlayerTest extends \PHPUnit_Framework_TestCase
{
    public function test_does_not_play_run_once_projectors()
    {
        /** @var ProjectorPlayer $player */
        $player = App::make(ProjectorPlayer::class);

        /** @var InMemory\ProjectorPositionLedger $projector_position_repo */
        $projector_position_repo = App::make(ProjectorPositionLedger::class);
        $projector_position_repo->reset();

        $player->play();

        $stored_projector_positions = $projector_position_repo->all();

        $actual = $stored_projector_positions->references();

        $expected = new ProjectorReferenceCollection([
            ProjectorReference::makeFromClass(RunFromLaunch::class),
            ProjectorReference::makeFromClass(RunFromStart::class)
        ]);

        $this->assertEquals($expected, $actual);
    }
}