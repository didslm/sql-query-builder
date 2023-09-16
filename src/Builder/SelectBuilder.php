<?php

namespace Didslm\QueryBuilder\Builder;

use Closure;
use Didslm\QueryBuilder\Components\OrImplRaw;
use Didslm\QueryBuilder\Components\WhereRaw;
use Didslm\QueryBuilder\Interface\GroupConditionInterface;
use Didslm\QueryBuilder\Components\In;
use Didslm\QueryBuilder\Components\Joins\FullJoin;
use Didslm\QueryBuilder\Components\Joins\InnerJoin;
use Didslm\QueryBuilder\Components\Joins\Join;
use Didslm\QueryBuilder\Components\Joins\LeftJoin;
use Didslm\QueryBuilder\Components\Joins\RightJoin;
use Didslm\QueryBuilder\Components\Like;
use Didslm\QueryBuilder\Components\OrderBy;
use Didslm\QueryBuilder\Components\OrImpl;
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

    public function where(string|Closure $column, mixed $value = null, ?string $operator = null): self
    {
        if($column instanceof Closure) {

            $_raw = $this->whereRawCallback($column);


            $this->wheres[] = new WhereRaw($_raw);

        } else {
            if(empty($value)) {
                throw new \InvalidArgumentException("When using `where`, value cannot be null");
            }

            $this->wheres[] = new Where($column, $value, $operator);
        }
        return $this;
    }

    public function and(string|Closure $column, mixed $value = null, ?string $operator = null): self
    {
        $lastWhere = array_pop($this->wheres);

        if($column instanceof Closure) {

            $_raw = $this->whereRawCallback($column);

            // var_dump($lastWhere);

            // if ($lastWhere instanceof GroupConditionInterface) {
                // $lastWhere->addCondition(new WhereRaw($_raw));
                $this->wheres[] = $lastWhere;
                $this->wheres[] = new WhereRaw($_raw);
            // } else {
                // $this->wheres[] = $lastWhere;
                // $this->wheres[] = new WhereRaw($_raw);
            // }

        } else {

            if($value === null) {
                throw new \InvalidArgumentException("When using `or` where, value cannot be null");
            }

            if ($lastWhere instanceof GroupConditionInterface) {
                $lastWhere->addCondition(new Where($column, $value, $operator));
                $this->wheres[] = $lastWhere;
            } else {
                $this->wheres[] = new Where($column, $value, $operator);
            }
        }
        return $this;

    }

    private function whereRawCallback($callback) {
        $_virtual = new SelectBuilder();
        $_virtual->fromTable = "";

        $callback($_virtual);

        $_raw = $_virtual->getWhere();

        return $_raw;

    }

    public function or(string|Closure $column, mixed $value = null, ?string $operator = null): self
    {

        if($column instanceof Closure) {

            $_raw = $this->whereRawCallback($column);

            $this->wheres[] = new OrImplRaw($_raw);

        } else {

            if($value === null) {
                throw new \InvalidArgumentException("When using `or` where, value cannot be null");
            }

            $this->wheres[] = new OrImpl($column, $value, $operator);
        }
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

    public function rightJoin(string $table, string $column, string $reference): self
    {
        $this->joins[] = new RightJoin($table, $column, $reference);

        return $this;
    }

    public function innerJoin(string $table, string $column, string $reference): self
    {
        $this->joins[] = new InnerJoin($table, $column, $reference);

        return $this;
    }

    public function fullJoin(string $table, string $column, string $reference): self
    {
        $this->joins[] = new FullJoin($table, $column, $reference);

        return $this;
    }

    public function limit(int $start, int $offset): self
    {
        $this->limitStart = $start;
        $this->limitOffset = $offset;
        return $this;
    }

    public function getWhere()
    {
        return $this->build()->getWhere();
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
