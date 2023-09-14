<?php

namespace Didslm\QueryBuilder\Builder;

use Didslm\QueryBuilder\Components\In;
use Didslm\QueryBuilder\Components\Joins\InnerJoin;
use Didslm\QueryBuilder\Components\Joins\Join;
use Didslm\QueryBuilder\Components\Joins\LeftJoin;
use Didslm\QueryBuilder\Components\Like;
use Didslm\QueryBuilder\Components\OrderBy;
use Didslm\QueryBuilder\Components\Regex;
use Didslm\QueryBuilder\Components\Table;
use Didslm\QueryBuilder\Components\Where;
use Didslm\QueryBuilder\Queries\QueryType;
use Didslm\QueryBuilder\Queries\Select;

class SelectBuilder implements Builder
{
    protected array $columns = [];
    protected string $fromTable;
    protected array $wheres = [];
    protected array $orders = [];
    protected array $joins = [];
    protected int $limitStart;
    protected int $limitOffset;

    public static function from(string $table): self
    {
        $queryBuilder = new self();
        $queryBuilder->fromTable = $table;
        $queryBuilder->select('*');
        return $queryBuilder;
    }

    public function table(string $table): self {
        $this->fromTable = $table;
        return $this;
    }

    public function select(string ...$fields): self
    {
        $this->columns = array_map(function ($field) {
            if (str_contains($field, '.')) {
                return $field;
            }
            return sprintf('%s.%s', $this->fromTable, $field);
        }, $fields);

        return $this;
    }

    public function where(string $column, mixed $value, ?string $operator = null): self
    {
        $this->wheres[] = new Where($column, $value, $operator);
        return $this;
    }
    
    public function in(string $column, array $values): self
    {
        $this->wheres[] = new In($column, $values);
        return $this;
    }

    public function like(string $column, string $value): self
    {
        $this->wheres[] = new Like($column, $value);
        return $this;
    }

    public function regex(string $column, string $value): self
    {
        $this->wheres[] = new Regex($column, $value);
        return $this;
    }

    public function join(Join $join) : self
    {
        $this->joins[] = $join;
        return $this;
    }

    public function leftJoin(string $table, string $column, string $reference): self
    {
        $this->joins[] = new LeftJoin($table, $column, $reference);

        return $this;
    }

    public function innerJoin(string $table, string $column, string $reference): self
    {
        $this->joins[] = new InnerJoin($table, $column, $reference);

        return $this;
    }

    public function limit(int $start, int $offset): self
    {
        $this->limitStart = $start;
        $this->limitOffset = $offset;
        return $this;
    }

    public function build(): QueryType
    {
        $select = new Select(new Table($this->fromTable));

        foreach ($this->columns as $column) {
            $select->addColumn($column);
        }

        foreach ($this->wheres as $where) {
            $select->addWhere($where);
        }

        foreach ($this->joins as $join) {
            $select->addJoin($join);
        }

        foreach ($this->orders as $order) {
            $select->addSorting($order);
        }

        return $select;
    }

    /**
     * Add an order by clause to the query.
     *
     * @param string $field
     * @param string $order
     * @return $this
     */
    public function orderBy(string $field, string $order = 'ASC'): self
    {
        $this->orders[] = new OrderBy($field, $order);
        return $this;
    }

}
