<?php namespace BoundedContext\Contracts\Schema;

interface Schema
{
    /**
     * Gets the schema as an array.
     *
     * @return array
     */

    public function get();

    /**
     * Adds a new structure to the Schema by key.
     *
     * @param string $key
     * @param \Closure|null $changes
     * @return void
     */

    public function add($key, \Closure $changes = null);

    /**
     * Upgrades an existing structure to the Schema by key.
     *
     * @param string $key
     * @param \Closure|null $changes
     * @return void
     */

    public function upgrade($key, \Closure $changes);

    /**
     * Renames an existing key in the current Schema.
     *
     * @param string $old_key
     * @param string $new_key
     * @return void
     */

    public function rename($old_key, $new_key);

    /**
     * Removes an existing key in the current Schema.
     *
     * @param string $key
     * @return void
     */

    public function remove($key);
}
