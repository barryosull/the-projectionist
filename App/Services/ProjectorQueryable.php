<?php namespace App\Services;

use App\ValueObjects\ProjectorReference;
use App\ValueObjects\ProjectorReferenceCollection;
use App\ValueObjects\ProjectorPosition;

class ProjectorQueryable
{
    private $projector_position_repository;
    private $projector_registerer;

    public function __construct(
        ProjectorPositionRepository $projector_position_repository,
        ProjectorRegisterer $projector_registerer
    ) {
        $this->projector_position_repository = $projector_position_repository;
        $this->projector_registerer = $projector_registerer;
    }

    public function newProjectors(): ProjectorReferenceCollection
    {
        $projector_positions = $this->projector_position_repository->all();

        $projector_references = $this->allProjectors();

        $new_projectors = [];

        foreach ($projector_references as $projector_reference) {
            $projector_position = $this->findLatest($projector_positions, $projector_reference);
            if ($this->isNew($projector_position)) {
                $new_projectors[] = $projector_reference;
            }
        }

        return new ProjectorReferenceCollection($new_projectors);
    }

    private function findLatest($projector_positions, ProjectorReference $projector_reference)
    {
        foreach ($projector_positions as $projector_position) {
            if ($this->isLatest($projector_position, $projector_reference)) {
                return $projector_position;
            }
        }
        return null;
    }

    private function isLatest(ProjectorPosition $projector_position, ProjectorReference $projector_reference)
    {
        return $projector_position->projector_reference->class_path == $projector_reference->class_path
            && $projector_position->projector_version == $projector_reference->currentVersion();
    }

    private function isNew($projector_position): bool
    {
        /** @var  ProjectorPosition $projector_position */
        if (!$projector_position) {
            return true;
        }

        $player_id = $projector_position->projector_reference;

        $actual_player_version = $player_id->currentVersion();
        $active_player_version = $projector_position->projector_version;

        return $actual_player_version > $active_player_version;
    }

    public function allProjectors(): ProjectorReferenceCollection
    {
        $list = $this->projector_registerer->all();
        return new ProjectorReferenceCollection($list);
    }
}