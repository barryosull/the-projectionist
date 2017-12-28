<?php namespace Tests\Unit\App\Services;

use App\Services\EventClassProjectorPlayer;
use App\Services\Projectionist;
use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;
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
}