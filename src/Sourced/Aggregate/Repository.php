<?php namespace BoundedContext\Sourced\Aggregate;

use BoundedContext\Contracts\Command\Command;
use BoundedContext\Contracts\Event\Snapshot\Stream;
use BoundedContext\Contracts\Event\Factory as EventFactory;
use BoundedContext\Contracts\Event\Snapshot\Factory as EventSnapshotFactory;
use BoundedContext\Contracts\Sourced\Aggregate\Factory as AggregateFactory;
use BoundedContext\Contracts\Sourced\Aggregate\State\Repository as StateRepository;
use BoundedContext\Contracts\Sourced\Aggregate\Aggregate;
use BoundedContext\Contracts\Sourced\Aggregate\State\Snapshot\Snapshot;
use BoundedContext\Contracts\Sourced\Log\Log;
use BoundedContext\Contracts\ValueObject\Identifier;

class Repository implements \BoundedContext\Contracts\Sourced\Aggregate\Repository
{
    private $state_repository;
    private $aggregate_factory;
    private $event_snapshot_factory;
    private $event_factory;
    private $event_snapshot_stream;
    private $log;

    public function __construct(
        StateRepository $state_repository,
        AggregateFactory $aggregate_factory,
        EventSnapshotFactory $event_snapshot_factory,
        EventFactory $event_factory,
        Stream $event_snapshot_stream,
        Log $log
    )
    {
        $this->state_repository = $state_repository;
        $this->aggregate_factory = $aggregate_factory;
        $this->event_snapshot_factory = $event_snapshot_factory;
        $this->event_factory = $event_factory;
        $this->event_snapshot_stream = $event_snapshot_stream;
        $this->log = $log;
    }

    public function by_command(Command $command)
    {
        $state = $this->state_repository->by_command($command);


    }

    public function id(Identifier $id)
    {
        $aggregate = $this->aggregate_factory->create(
            $this->snapshot_repository->id($id)
        );

        $event_snapshots = $this->event_snapshot_stream
            ->after($aggregate->version())
            ->with($aggregate->id())
            ->get();

        foreach($event_snapshots as $snapshot)
        {
            $aggregate->apply(
                $this->event_factory->snapshot($snapshot)
            );
        }

        $aggregate->flush();

        return $aggregate;
    }

    public function save(Aggregate $aggregate)
    {
        $this->snapshot_repository->save(
            $aggregate->snapshot()
        );

        $this->log->append_collection(
            $this->event_snapshot_factory->collection(
                $aggregate->changes()
            )
        );

        $aggregate->flush();
    }
}