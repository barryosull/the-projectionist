<?php namespace Tests\Acceptance;

use App\Services\ProjectorPositionRepository;
use App\Usecases\ProjectorPlayer;
use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;
use App\ValueObjects\ProjectorReferenceCollection;
use Bootstrap\App;
use Infrastructure\App\Services\InMemoryProjectorPositionRepository;
use Tests\Fakes\Projectors\RunFromLaunch;
use Tests\Fakes\Projectors\RunFromStart;

class ProjectorPlayerTest extends \PHPUnit_Framework_TestCase
{
    public function test_does_not_play_run_once_projectors()
    {
        /** @var ProjectorPlayer $player */
        $player = App::make(ProjectorPlayer::class);

        /** @var InMemoryProjectorPositionRepository $projector_position_repo */
        $projector_position_repo = App::make(ProjectorPositionRepository::class);
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