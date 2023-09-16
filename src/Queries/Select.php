<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Queries;

use Didslm\QueryBuilder\Components\ConditionInterface;
use Didslm\QueryBuilder\Components\GroupCondition;
use Didslm\QueryBuilder\Components\Joins\Join;
use Didslm\QueryBuilder\Components\OrderBy;
use Didslm\QueryBuilder\Components\Table;
use Didslm\QueryBuilder\Utilities\AliasResolver;

class Select implements QueryType
{
    const DEFAULT_OPERATOR = 'AND';
    private array $conditions = [];
    private array $joins = [];
    private array $columns = [];
    private array $orders = [];

    public function __construct(private Table $table){}

    public function addColumn(string $column): Select
    {
        $this->columns[] = $column;
        return $this;
    }

    public function addCondition(ConditionInterface $condition): Select
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public function addGroup(GroupCondition $groupCondition): self
    {
        $this->conditions[] = $groupCondition;
        return $this;
    }

    public function addJoin(Join $join): Select
    {
        $this->joins[] = $join;
        return $this;
    }

    public function addSorting(OrderBy $order): Select
    {
        $this->orders[] = $order;
        return $this;
    }

    public function __toString(): string
    {
        return $this->toSql();
    }

    public function toSql(): string
    {
        $select = implode(', ', array_map(fn($column) => AliasResolver::resolve($this->table, $column), $this->columns));
        $sql = "SELECT {$select} FROM {$this->table->toSql()}";

        if (count($this->joins) > 0) {
            $sql .= ' ';
            $sql .= implode(' ', array_map(fn($join) => $join->toSql(), $this->joins));
        }

        if (count($this->conditions) > 0) {
            $sql = sprintf('%s WHERE %s', $sql, implode(' AND ', array_map(fn($condition) => $condition->toSql(), $this->conditions)));
        }

        if (count($this->orders) > 0) {
            $sql .= ' ORDER BY ';
            $sql .= implode(', ', array_map(fn($order) => $order->toSql(), $this->orders));
        }
        return $sql;
    }
}
