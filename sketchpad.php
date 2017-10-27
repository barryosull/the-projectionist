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
				- Write the factories for getting the above subsets of workflows and projectors (done)

			- ProcessEvents makes use 
		- Extract how events are played into projectors as a strategy
		- Have one that just plays from where each projector left off
		- Have a smarter one that play events one by one to projectors if they're at the same position
*/

/** Usecases */
class ProjectorBooter
{
    private $projector_queryable;
    private $projector_skipper;
    private $projectors_player;

    public function __construct(ProjectorQueryable $projector_queryable, ProjectorSkipper $projector_skipper, ProjectorsPlayer $projectors_player)
    {
        $this->projector_queryable = $projector_queryable;
        $this->projector_skipper = $projector_skipper;
        $this->projectors_player = $projectors_player;
    }

    public function boot()
    {
        $new_projectors = $this->projector_queryable->newProjectors();

        $run_once_projectors = $new_projectors->extract(WorkflowModes::RUN_FROM_LAUCNH);
        $this->projector_skipper->skipToLastEvent($run_once_projectors);

        $standard_projectors = $new_projectors->exclude(WorkflowModes::RUN_FROM_LAUCNH);
        $this->projectors_player->play($standard_projectors);
    }
}

class EventProcessor
{
    private $projectors_player;

    public function __construct(ProjectorsPlayer $projectors_player)
    {
        $this->projectors_player = $projectors_player;
    }

    public function process()
    {
        $projectors = $this->projector_queryable->allProjectors();

        $active_projectors = $projectors->extract(WorkflowModes::RUN_ONCE);
        $this->projectors_player->play($active_projectors);
    }
}

/** Services */

// Lives at Application layer, though RegisteredList should be an interface with an implementation, as it's coupled to laravel
class ProjectorQueryable
{
    private $snapshot_repository;
    private $player_list;

    public function __construct(
        PlayerSnapshotRepository $snapshot_repository,
        RegisteredList $player_list
    ) {
        $this->snapshot_repository = $snapshot_repository;
        $this->player_list = $player_list;
    }

    public function newProjectors(): array
    {
        $snapshots = $this->snapshot_repository->all();

        $projector_ids = $this->allProjectors();

        $new_projectors = [];

        foreach ($projector_ids as $projector_id) {
            $snapshot = $snapshots->findLatest($projector_id);
            if ($this->isNew($snapshot, $projector_id)) {
                $new_projectors[] = $projector_id;
            }
        }

        return $new_projectors;
    }

    private function isNew($snapshot): boolean
    {
        if (!$snapshot) {
            return false;
        }
        $player_id = $snapshot->player_class()->value();
        $actual_player_version = $player_id::version();

        $active_player_version = $snapshot->player_version()->value();

        return $actual_player_version > $active_player_version;
    }

    public function allProjectors()
    {
        $list = $this->player_list->list();
        return $list->application;
    }
}

// Lives at Application layer
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
		$this->snapshot_respository = $snapshot_respository;
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

	private function playProjector(ProjectorId $projector_id)
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

// Lives at Application layer
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

// Lives at Application layer
class ProjectorSkipper
{
	private $snapshot_respository;
	private $event_store;

	public function __constructor(
		ProjectorSnapshotRepository $snapshot_respository, 
		EventStore $event_store
	) {
		$this->snapshot_respository = $snapshot_respository;
		$this->event_store = $event_store;
	}

	public function skipToLastEvent($workflow_ids)
	{
		$latest_event = $this->event_store->latestEvent();
		foreach ($workflow_ids as $workflow_id) {
			$this->skipProjectorToEvent($workflow_id, $latest_event);
		}
	}

	private function skipProjectorToEvent($workflow_id, $latest_event)
	{
		$workflow_snapshot = $this->snapshot_respository->fetch($workflow_id);

		$workflow_snapshot->skip($latest_event);

		$this->snapshot_respository->store($workflow_snapshot);
	}
}

// Coupled to laravel, so this is infrastructure
class ProjectorLoader
{
	public function load($projector_class)
	{
		return app($projector_class);
	}
}

// Coupled to laravel, so this is infrastructure
class ProjectorRepository
{
    private $table;
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->table = \DB::connection()->table('player_snapshots');
        $this->serializer = $serializer;
    }

    public function store($projector)
    {
        $snapshot = $projector->snapshot();

        $row = $this->serializer->serialize($snapshot);

        $key = [
            'class_name', $row->class_name,
            'player_version', $row->player_version
        ];

        $this->table->updateOrCreate($key, $row);
    }

    public function fetch($projector_id)
    {
        $row = $this->table
            ->where('class_name', $projector_id)
            ->where('player_version', $projector_id::version)
            ->first();

        if (!$row) {
            return null;
        }

        $snapshot = $this->convertRowToSnapshot($row);

        return $snapshot;
    }

    private function convertRowToSnapshot($row)
    {
        return new Snapshot(
            new ClassName($row['class_name']),
            new Integer($row['version']),
            new Integer($row['player_version']),
            $this->datetime_generator->string($row['occurred_at']),
            $this->identifier_generator->string($row['last_id'])
        );
    }
}
