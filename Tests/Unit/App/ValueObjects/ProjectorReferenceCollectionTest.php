<?php namespace Tests\Unit\ValueObjects;

use Projectionist\App\ValueObjects\ProjectorMode;
use Projectionist\App\ValueObjects\ProjectorReference;
use Projectionist\App\ValueObjects\ProjectorReferenceCollection;
use Tests\Fakes\Projectors\RunFromStart;
use Tests\Fakes\Projectors\RunOnce;

class ProjectorReferenceCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function test_will_not_allow_duplicates()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Duplicate projector references, not allowed");

        $ref_1 = ProjectorReference::makeFromClass(RunFromStart::class);
        new ProjectorReferenceCollection([$ref_1, $ref_1]);
    }

    public function test_excluding_by_mode()
    {
        $ref_1 = ProjectorReference::makeFromClass(RunFromStart::class);
        $ref_2 = ProjectorReference::makeFromClass(RunOnce::class);
        $collection = new ProjectorReferenceCollection([$ref_1, $ref_2]);

        $excluded = $collection->exclude(ProjectorMode::RUN_ONCE);

        $this->assertEquals([$ref_1], $excluded->all());
    }

    public function test_extracting_by_mode()
    {
        $ref_1 = ProjectorReference::makeFromClass(RunFromStart::class);
        $ref_2 = ProjectorReference::makeFromClass(RunOnce::class);
        $collection = new ProjectorReferenceCollection([$ref_1, $ref_2]);

        $extracted = $collection->extract(ProjectorMode::RUN_ONCE);

        $this->assertEquals([$ref_2], $extracted->all());
    }
}