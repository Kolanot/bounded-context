<?php namespace BoundedContext\Sourced\Stream;

use BoundedContext\Collection\Collection;
use BoundedContext\Contracts\Event\Snapshot\Factory as EventSnapshotFactory;
use BoundedContext\Contracts\Event\Factory as EventFactory;
use BoundedContext\ValueObject\Integer as Integer_;

abstract class AbstractStream
{
    /**
     * @var EventFactory
     */
    protected $event_factory;

    /**
     * @var EventSnapshotFactory
     */
    protected $event_snapshot_factory;

    protected $limit;
    protected $chunk_size;

    /**
     * @var Integer_
     */
    protected $streamed_count;

    /**
     * @var Collection
     */
    protected $event_snapshot_schemas;

    public function __construct(
        EventFactory $event_factory,
        EventSnapshotFactory $event_snapshot_factory,
        Integer_ $limit,
        Integer_ $chunk_size
    )
    {
        $this->event_factory = $event_factory;
        $this->event_snapshot_factory = $event_snapshot_factory;

        $this->limit = $limit;
        $this->chunk_size = $chunk_size;

        $this->rewind();
    }

    protected function reset()
    {
        $this->streamed_count = new Integer_(0);
        $this->event_snapshot_schemas = new Collection();
    }

    /**
     * Fetches the next set of event snapshot schemas.
     * @return void
     */
    abstract protected function fetch();

    protected function is_unlimited()
    {
        return ($this->limit->equals(new Integer_(0)));
    }

    protected function has_more_chunks()
    {
        return ($this->event_snapshot_schemas->count()->serialize() < $this->chunk_size->serialize());
    }

    public function current()
    {
        return $this->event_snapshot_schemas->current();
    }

    public function next()
    {
        $this->event_snapshot_schemas->next();

        if(!$this->event_snapshot_schemas->valid() && $this->has_more_chunks())
        {
            $this->fetch();
        }

        $this->streamed_count = $this->streamed_count->increment();
    }

    public function key()
    {
        return $this->event_snapshot_schemas->key();
    }

    public function valid()
    {
        if($this->streamed_count->equals($this->limit) && !$this->is_unlimited())
        {
            return false;
        }

        return $this->event_snapshot_schemas->valid();
    }

    public function rewind()
    {
        $this->reset();
        $this->fetch();
    }
}
