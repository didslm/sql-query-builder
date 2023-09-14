<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Queries;

use Didslm\QueryBuilder\Components\Condition;
use Didslm\QueryBuilder\Components\Table;
use Didslm\QueryBuilder\Utilities\Cleaner;

class Update 
{
    private array $conditions = [];
    private array $columns = [];
    private array $values = [];

    public function __construct(private Table $table){}

    public function setColumns(array $values): Update
    {
        $this->values = $values;
        return $this;
    }

    public function setValues(array $values): Update
    {
        $this->values = $values;
        return $this;
    }

    public function where(Condition $condition): Update
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public function toSql(): string
    {
        $this->values = array_map(fn($value) => Cleaner::clean($value), $this->values);
        
        $sql = "UPDATE {$this->table->toSql()} SET ";
        $sql .= implode(', ', array_map(fn($column, $value) => "{$column} = {$value}", $this->columns, $this->values));
        if (count($this->conditions) > 0) {
            $sql .= ' WHERE ';
            $sql .= implode(' AND ', array_map(fn($condition) => $condition->toSql(), $this->conditions));
        }
        return $sql;
    }


}
