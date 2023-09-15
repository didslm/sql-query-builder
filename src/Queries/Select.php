<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Queries;

use Didslm\QueryBuilder\Components\Condition;
use Didslm\QueryBuilder\Components\Joins\Join;
use Didslm\QueryBuilder\Components\OrderBy;
use Didslm\QueryBuilder\Components\OrImlp;
use Didslm\QueryBuilder\Components\Table;
use Didslm\QueryBuilder\Utilities\AliasResolver;

class Select implements QueryType
{
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

    public function addWhere(Condition $condition): Select
    {
        $this->conditions[] = $condition;
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
            $first = array_shift($this->conditions);
            $sql .= ' WHERE ' . $first->toSql();

            foreach ($this->conditions as $condition) {
                $andOr = $condition instanceof OrImlp ? ' OR ' : ' AND ';
                $sql .= $andOr.$condition->toSql();
            }
        }

        if (count($this->orders) > 0) {
            $sql .= ' ORDER BY ';
            $sql .= implode(', ', array_map(fn($order) => $order->toSql(), $this->orders));
        }
        return $sql;
    }
}
