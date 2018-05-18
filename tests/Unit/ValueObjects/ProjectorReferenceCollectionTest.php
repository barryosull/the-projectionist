<?php namespace ProjectonistTests\Unit\ValueObjects;

use Projectionist\Domain\ValueObjects\ProjectorMode;
use Projectionist\Domain\ValueObjects\ProjectorReference;
use Projectionist\Domain\ValueObjects\ProjectorReferenceCollection;
use ProjectonistTests\Fakes\Projectors\RunFromStart;
use ProjectonistTests\Fakes\Projectors\RunOnce;

class ProjectorReferenceCollectionTest extends \PHPUnit\Framework\TestCase
{
    public function test_will_not_allow_duplicates()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Duplicate projector references, not allowed");

        $ref_1 = ProjectorReference::makeFromProjector(new RunFromStart);
        new ProjectorReferenceCollection([$ref_1, $ref_1]);
    }

    public function test_excluding_by_mode()
    {
        $ref_1 = ProjectorReference::makeFromProjector(new RunFromStart);
        $ref_2 = ProjectorReference::makeFromProjector(new RunOnce);
        $collection = new ProjectorReferenceCollection([$ref_1, $ref_2]);

        $excluded = $collection->exclude(ProjectorMode::RUN_ONCE);

        $this->assertEquals([$ref_1], $excluded->all());
    }

    public function test_extracting_by_mode()
    {
        $ref_1 = ProjectorReference::makeFromProjector(new RunFromStart);
        $ref_2 = ProjectorReference::makeFromProjector(new RunOnce);
        $collection = new ProjectorReferenceCollection([$ref_1, $ref_2]);

        $extracted = $collection->extract(ProjectorMode::RUN_ONCE);

        $this->assertEquals([$ref_2], $extracted->all());
    }
}