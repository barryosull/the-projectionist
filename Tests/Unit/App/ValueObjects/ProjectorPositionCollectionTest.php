<?php namespace Tests\Unit\ValueObjects;

use Projectionist\App\ValueObjects\ProjectorPosition;
use Projectionist\App\ValueObjects\ProjectorPositionCollection;
use Projectionist\App\ValueObjects\ProjectorReference;
use Tests\Fakes\Projectors\RunFromStart;
use Tests\Fakes\Projectors\RunOnce;

class ProjectorPositionCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function test_if_a_collection_has_a_reference()
    {
        $ref_1 = ProjectorReference::makeFromClass(RunFromStart::class);
        $ref_1_bumped = ProjectorReference::make(RunFromStart::class, 2);
        $ref_2 = ProjectorReference::makeFromClass(RunOnce::class);

        $collection = new ProjectorPositionCollection([
            ProjectorPosition::makeNewUnplayed($ref_1),
            ProjectorPosition::makeNewUnplayed($ref_2),
        ]);

        $this->assertTrue($collection->hasReference($ref_1));
        $this->assertTrue($collection->hasReference($ref_2));
        $this->assertFalse($collection->hasReference($ref_1_bumped));
    }

    public function test_will_not_allow_duplicate_references()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Duplicate projector references, not allowed");

        $ref_1 = ProjectorReference::makeFromClass(RunFromStart::class);
        new ProjectorPositionCollection([
            ProjectorPosition::makeNewUnplayed($ref_1),
            ProjectorPosition::makeNewUnplayed($ref_1),
        ]);
    }
}