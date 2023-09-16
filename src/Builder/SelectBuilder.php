<?php

namespace Didslm\QueryBuilder\Builder;


use Didslm\QueryBuilder\Components\AndGroup;
use Didslm\QueryBuilder\Components\In;
use Didslm\QueryBuilder\Components\Joins\FullJoin;
use Didslm\QueryBuilder\Components\Joins\InnerJoin;
use Didslm\QueryBuilder\Components\Joins\Join;
use Didslm\QueryBuilder\Components\Joins\LeftJoin;
use Didslm\QueryBuilder\Components\Joins\RightJoin;
use Didslm\QueryBuilder\Components\Like;
use Didslm\QueryBuilder\Components\OrderBy;
use Didslm\QueryBuilder\Components\OrGroup;
use Didslm\QueryBuilder\Components\Regex;
use Didslm\QueryBuilder\Components\Table;
use Didslm\QueryBuilder\Components\Condition;
use Didslm\QueryBuilder\Queries\QueryType;
use Didslm\QueryBuilder\Queries\Select;

class SelectBuilder implements Builder
{
    private const AND_OPERATOR = 'AND';
    private const OR_OPERATOR = 'OR';
    private const DEFAULT_GROUP_OPERATOR = self::AND_OPERATOR;

    protected array $columns = [];
    protected string $fromTable;
    private array $conditions = [];

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
        $this->conditions[] = AndGroup::create(new Condition($column, $value, $operator));
        return $this;
    }

    public function and(string $column, mixed $value, ?string $operator = null): self
    {
        $lastGroup = array_pop($this->conditions);
        if ($lastGroup === null) {
            return $this->where($column, $value, $operator);
        }

        $lastGroup->addGroupCondition(new Condition($column, $value, $operator));
        $this->conditions[] = $lastGroup;
        return $this;
    }

    public function or(string $column, mixed $value, ?string $operator = null): self
    {
        $this->conditions[] = OrGroup::create(new Condition($column, $value, $operator));

        return $this;
    }

    public function orGroup(): self
    {
        $this->conditions = [OrGroup::create(
            ...$this->conditions
        )];
        return $this;
    }

    public function in(string $column, array $values): self
    {
        $this->conditions[] = AndGroup::create(new In($column, $values));
        return $this;
    }

    public function like(string $column, string $value): self
    {
        $this->conditions[] = AndGroup::create(new Like($column, $value));
        return $this;
    }

    public function regex(string $column, string $value): self
    {
        $this->conditions[] = AndGroup::create(new Regex($column, $value));
        return $this;
    }

    public function join(Join $join) : self
    {
        $this->joins[] = $join;
        return $this;
    }

    public function leftJoin(string $table, string $column, string $reference): self
    {
        $this->joins[] = (new LeftJoin($table, $column, $reference))
            ->setParentTable(new Table($this->fromTable));

        return $this;
    }

    public function rightJoin(string $table, string $column, string $reference): self
    {
        $this->joins[] = (new RightJoin($table, $column, $reference))
            ->setParentTable(new Table($this->fromTable));

        return $this;
    }

    public function innerJoin(string $table, string $column, string $reference): self
    {
        $this->joins[] = (new InnerJoin($table, $column, $reference))->setParentTable(new Table($this->fromTable));

        return $this;
    }

    public function fullJoin(string $table, string $column, string $reference): self
    {
        $this->joins[] = (new FullJoin($table, $column, $reference))->setParentTable(new Table($this->fromTable));

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

        foreach ($this->conditions as $condition) {
            $select->addGroup($condition);
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
