<?php

namespace Didslm\QueryBuilder\Builder;

class InsertBuilder
{
    private string $table;
    private array $columns;
    private array $values;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function addColumns(array $columns): InsertBuilder
    {
        $this->columns = $columns;
        return $this;
    }

    public function addValues(array $values): InsertBuilder
    {
        if (count($this->columns) !== count($values)) {
            throw new \Exception('Number of columns and values do not match');
        }

        $this->values = $values;
        return $this;
    }

    public function toSql(): string
    {
        $columns = implode(', ', $this->columns);
        $values = implode("', '", $this->values);

        return sprintf(
            "INSERT INTO %s (%s) VALUES ('%s')",
            $this->table,
            $columns,
            $values
        );
    }

    public function __toString(): string
    {
        return $this->toSql();
    }

    public static function into(string $table): InsertBuilder
    {
        return new static($table);
    }
}
