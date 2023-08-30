<?php

namespace Didslm\QueryBuilder\Builder;

use Didslm\QueryBuilder\Components\InnerJoin;
use Didslm\QueryBuilder\Components\LeftJoin;
use Didslm\QueryBuilder\Components\OrderBy;
use Didslm\QueryBuilder\Components\Regex;
use Didslm\QueryBuilder\Components\Select;
use Didslm\QueryBuilder\Components\Where;
use Didslm\QueryBuilder\QueryBuilderInterface;

class SelectBuilder implements SelectQueryBuilderInterface
{
    protected array $columns = [];
    protected string $fromTable;
    protected array $wheres = [];
    protected array $orders = [];
    protected array $joins = [];
    protected int $limitStart;
    protected int $limitOffset;

    public static function from(string $table): QueryBuilder
    {
        $queryBuilder = new self();
        $queryBuilder->fromTable = $table;
        $queryBuilder->select(Select::ALL);
        return $queryBuilder;
    }

    public function table(string $table): QueryBuilder {
        $this->fromTable = $table;
        return $this;
    }

    public function select(string ...$fields): QueryBuilder
    {
        $this->columns = array_map(function ($field) {
            if (str_contains($field, '.')) {
                return $field;
            }
            return sprintf('%s.%s', $this->fromTable, $field);
        }, $fields);

        return $this;
    }

    public function where(string $field, string $value, string $operator = '='): QueryBuilder
    {
        $this->wheres[] = new Where($field, $value, $operator);
        return $this;
    }

    public function whereRegex(string $column, string $pattern): QueryBuilder
    {
        $this->wheres[] = new Regex($column, $pattern);
        return $this;
    }

    public function leftJoin(string $table, string $column, string $reference): QueryBuilder
    {
        $this->joins[] = new LeftJoin($table, $column, $reference);

        return $this;
    }

    public function innerJoin(string $table, string $column, string $reference): QueryBuilder
    {
        $this->joins[] = new InnerJoin($table, $column, $reference);

        return $this;
    }

    public function limit(int $start, int $offset): QueryBuilder
    {
        $this->limitStart = $start;
        $this->limitOffset = $offset;
        return $this;
    }

    /**
     * Add an order by clause to the query.
     *
     * @param string $field
     * @param string $order
     * @return $this
     */
    public function orderBy(string $field, string $order = 'ASC'): QueryBuilder
    {
        $this->orders[] = new OrderBy($field, $order);
        return $this;
    }

    public function __toString(): string
    {
        return $this->toSql();
    }

    public function toSql(): string
    {
        $sql = "SELECT " . implode(", ", $this->columns) . " FROM " . $this->fromTable;

        if (!empty($this->joins)) {
            $sql .= " " . implode(" ", array_map(function ($join) {
                    return $join->toSql();
                }, $this->joins));
        }

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(" AND ", array_map(function ($where) {
                    return $where->toSql();
                }, $this->wheres));
        }

        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . implode(", ", array_map(function ($order) {
                    return $order->toSql();
                }, $this->orders));
        }

        return $sql;
    }

}