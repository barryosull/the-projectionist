<?php

/**
	TODO: Refactor projectors/workflows, remove reliance on abstract projectors/workflows

	Ideas
		- Remove $snapshot concept from $projector, move all logic into player (done)
		- Split Workflow and Projector players out into own concept, use hard classes (done)
			- No, this should be done at a higher level, something that makes use of the ProjectorsPlayers
				- Eg. In The service launch scripts
					$workflow_booter->boot($run_from_launch_workflows);
					$projector_player->play($run_once_workflows);
					$projector_player->play($standard_worflows);
					$projector_player->play($new_projectors);
				- Then in the events_process 
					$projector_player->play($workflows);
					$projector_player->play($projectors);
				- Write the factories for getting the above subsets of workflows and projectors

			- ProcessEvents makes use 
		- Extract how events are played into projectors as a strategy
		- Have one that just plays from where each projector left off
		- Have a smarter one that play events one by one to projectors if they're at the same position
*/
class ProjectorsPlayer
{
	private $snapshot_respository;
	private $projector_loader;
	private $event_store;
	private $projector_player;

	public function __constructor(
		ProjectorSnapshotRepository $snapshot_respository, 
		ProjectorLoader $projector_loader,
		EventStore $event_store, 
		ProjectorPlayer $projector_player
	) {
		$this->snapshot_respository = $projector_repository;
		$this->projector_loader = $projector_loader;
		$this->event_store = $event_store;
		$this->projector_player = $projector_player;
	}

	public function play($projector_ids)
	{
		foreach ($projector_ids as $projector_id) {
			$this->playProjector($projector_id);
		}
	}

	private fuction playProjector(ProjectorId $projector_id)
	{
		$projector_snapshot = $this->snapshot_respository->fetch($projector_id);
		$projector = $this->projector_loader->load($projector_id);

		$stream = $this->event_store->getStream($projector_snapshot->lastProcessedEvent());

		foreach ($stream as $event) {
			$projector_snapshot = $this->projector_player->play($event, $projector, $projector_snapshot);
			$this->snapshot_respository->store($projector_snapshot);
		}
	}
}

class ProjectorPlayer
{
	public function play($event, Projector $projector, $projector_snapshot)
	{
		if ($projector->canPlay($event)) {
			$projector->play($event);
			return $projector_snapshot->played($event);
		}
		return $projector_snapshot->skipped($event);
	}
}

class WorkflowBooter
{
	private $snapshot_respository;
	private $event_store;

	public function __constructor(
		ProjectorSnapshotRepository $snapshot_respository, 
		EventStore $event_store
	) {
		$this->snapshot_respository = $projector_repository;
		$this->event_store = $event_store;
	}

	public function boot($workflow_ids)
	{
		$latest_event = $this->event_store->latestEvent();
		foreach ($workflow_ids as $workflow_id) {
			$this->bootWorkflow($workflow_id, $latest_event);
		}
	}

	private function bootWorkflow($workflow_id, $latest_event)
	{
		$workflow_snapshot = $this->snapshot_respository->fetch($workflow_id);

		$workflow_snapshot->skip($latest_event);

		$this->snapshot_respository->store($projector_snapshot);
	}
}

class ProjectorLoader
{
	public function load($projector_class)
	{
		return app($projector_class);
	}
}

class ProjectorRepository
{
	public function store($projector)
	{
		$snapshot = $projector->snapshot();

		// Translate $snapshot to DB row $data

		$this->db->upsert($snapshot->id()->value(), $row);
	}

	public function fetch($projector_id)
	{
		$row = $this->db->fetch($projector_id);

		// Translate $row to $snapshot

		return 
	}
}
