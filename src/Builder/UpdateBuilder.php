<?php

namespace Didslm\QueryBuilder\Builder;

use Didslm\QueryBuilder\Components\InnerJoin;
use Didslm\QueryBuilder\Components\Where;

class UpdateBuilder implements QueryBuilder
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

    public function addColumns(array $columns): UpdateBuilder
    {
        $this->columns = $columns;
        return $this;
    }

    public function addValues(array $values): UpdateBuilder
    {
        if (count($this->columns) !== count($values)) {
            throw new \Exception('Number of columns and values do not match');
        }

        $this->values = $values;
        return $this;
    }

    public function where(string $field, string $value, string $operator = '='): UpdateBuilder
    {
        $this->wheres[] = new Where($field, $value, $operator);
        return $this;
    }

    public function innerJoin(string $table, string $column, string $reference): UpdateBuilder
    {
        $this->joins[] = new InnerJoin($table, $column, $reference);
        return $this;
    }
    public function table(string $table): UpdateBuilder
    {
        return new static($table);
    }

    public static function into(string $table): UpdateBuilder
    {
        return new static($table);
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

            $setStatements[] = (strpos($value, ':') === 0)
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
