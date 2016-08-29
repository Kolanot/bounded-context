<?php namespace BoundedContext\Event;

use EventSourced\ValueObject\ValueObject\Type\AbstractSingleValue;

class Type extends AbstractSingleValue
{
    protected function validator()
    {
        return parent::validator()->alnum("_.")->noWhitespace()->lowercase();
    }

    public function aggregate_type()
    {
        $parts = explode(".", $this->value());
        array_pop($parts);
        return new AggregateType(implode(".", $parts));
    }

    public static function from_event($object)
    {
        $class = strtolower(get_class($object));
        $parts = explode("\\", $class);
        unset($parts[0]);
        unset($parts[3]);
        unset($parts[5]);
        $parts = array_values($parts);
        return new Type(implode(".", $parts));
    }

    public function to_event_class()
    {
        $parts = explode(".", $this->value());
        $class_path = [
            "Domain",
            ucfirst($parts[0]),
            ucfirst($parts[1]),
            "Aggregate",
            ucfirst($parts[2]),
            "Event",
            ucfirst($parts[3])
        ];
        return implode("\\", $class_path);
    }
}
