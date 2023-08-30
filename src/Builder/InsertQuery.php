<?php

namespace Didslm\QueryBuilder\Builder;

class InsertQuery
{
    private string $table;
    private array $columns;
    private array $values;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function addColumns(array $columns): InsertQuery
    {
        $this->columns = $columns;
        return $this;
    }

    public function addValues(array $values): InsertQuery
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

    public static function into(string $table): InsertQuery
    {
        return new static($table);
    }
}