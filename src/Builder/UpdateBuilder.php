<?php

namespace Didslm\QueryBuilder\Builder;

use Didslm\QueryBuilder\Components\Joins\InnerJoin;
use Didslm\QueryBuilder\Components\Table;
use Didslm\QueryBuilder\Components\Condition;
use Didslm\QueryBuilder\Queries\Update;

class UpdateBuilder implements Builder
{
    private string $table;
    private array $columns;
    private array $values;
    private array $wheres = [];
    private array $joins = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function addColumns(array $columns):self
    {
        $this->columns = $columns;
        return $this;
    }

    public function addValues(array $values):self
    {
        if (count($this->columns) !== count($values)) {
            throw new \Exception('Number of columns and values do not match');
        }

        $this->values = $values;
        return $this;
    }

    public function where(string $column, mixed $value, ?string $operator = null):self
    {
        $this->wheres[] = new Condition($column, $value, $operator);
        return $this;
    }

    public function innerJoin(string $table, string $column, string $reference):self
    {
        $this->joins[] = new InnerJoin($table, $column, $reference);
        return $this;
    }
    public function table(string $table):self
    {
        return new static($table);
    }

    public static function into(string $table):self
    {
        return new static($table);
    }

    public function build(): Update
    {
        $update = new Update(new Table($this->table));

        foreach ($this->columns as $column) {
            $update->addColumn($column);
        }

        foreach ($this->values as $value) {
            $update->addValue($value);
        }

        foreach ($this->wheres as $where) {
            $update->addWhere($where);
        }

        foreach ($this->joins as $join) {
            $update->addJoin($join);
        }

        return $update;
    }

    public function toSql():string
    {
        $setStatements = [];
        $conditions = '';
        if (!empty($this->wheres)) {
            $conditions = "WHERE " . implode(" AND ", array_map(function ($where) {
                    return $where->toSql();
                }, $this->wheres));
        }

        for ($i = 0; $i < count($this->columns); $i++) {
            $column = $this->columns[$i];
            $value = $this->values[$i];

            $setStatements[] = (str_starts_with($value, ':'))
                ? "$column = $value"
                : "$column = '" . addslashes($value) . "'";
        }

        $setString = implode(', ', $setStatements);

        $joins = '';
        if (!empty($this->joins)) {
            $joins = implode(" ", array_map(function ($join) {
                return $join->toSql();
            }, $this->joins));
        }

        return sprintf("UPDATE %s %s SET %s %s", $this->table, $joins, $setString, $conditions);

    }

    public function __toString(): string
    {
        return $this->toSql();
    }

}
