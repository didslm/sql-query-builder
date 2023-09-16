<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Queries;

use Didslm\QueryBuilder\Interface\ConditionInterface;
use Didslm\QueryBuilder\Components\Joins\Join;
use Didslm\QueryBuilder\Components\Table;
use Didslm\QueryBuilder\Trait\getWhereTrait;
use Didslm\QueryBuilder\Utilities\AliasResolver;
use Didslm\QueryBuilder\Utilities\Cleaner;
use Didslm\QueryBuilder\Utilities\ValueResolver;

class Update implements QueryType
{
    use getWhereTrait;
    private array $conditions = [];
    private array $columns = [];
    private array $values = [];
    private array $joins = [];

    public function __construct(private Table $table){}

    public function addValue(string|int|float $value): Update
    {
        $this->values[] = Cleaner::clean($value);
        return $this;
    }

    public function addColumn(string $column): Update
    {
        $this->columns[] = $column;
        return $this;
    }

    public function addWhere(ConditionInterface $condition): Update
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public function addJoin(Join $join): Update
    {
        $this->joins[] = $join;
        return $this;
    }

    public function __toString(): string
    {
        return $this->toSql();
    }

    public function toSql(): string
    {
        $sql = sprintf('UPDATE %s', $this->table->getTable());

        if (count($this->joins) > 0) {
            $sql .= ' ';
            $sql .= implode(' ', array_map(fn($join) => $join->toSql(), $this->joins));
        }

        $sql .= ' SET '.implode(', ', array_map(fn($column, $value) => sprintf('%s = %s', AliasResolver::resolve($this->table, $column, count($this->joins) > 0), ValueResolver::resolve($value)), $this->columns, $this->values));

        if (count($this->conditions) > 0) {
            $sql .= ' WHERE ';
            $sql .= implode(' AND ', array_map(fn($condition) => $condition->toSql(), $this->conditions));
        }

        return $sql;
    }


}
