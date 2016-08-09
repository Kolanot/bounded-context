<?php namespace BoundedContext\Contracts\Event\Snapshot;

use BoundedContext\Contracts\Schema\Schema;
use BoundedContext\Contracts\Snapshot\Snapshot as SnapshotContract;
use BoundedContext\Contracts\Core\Identifiable;
use EventSourced\ValueObject\Contracts\ValueObject\Identifier;

interface Snapshot extends SnapshotContract, Identifiable
{
    /**
     * Gets the string encoded type of the event
     *
     * @return string
     */
    public function type();
    
    /**
     * Gets the type id of the Event.
     *
     * @return Identifier
     */
    public function type_id();
    
    /**
     * Get the root entity id that this event affects
     *
     * @return Identifier
     */
    public function root_entity_id();
    
    /**
     * Get the type of the aggregate type ID
     *
     * @return Identifier
     */
    public function aggregate_type_id();
    
    /**
     * Get the ID of the originating command
     *
     * @return Identifier
     */
    public function command_id();
    
    /**
     * Gets the current Schema.
     *
     * @return Schema
     */
    public function schema();
}
