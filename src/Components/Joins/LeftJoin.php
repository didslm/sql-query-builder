<?php

namespace Didslm\QueryBuilder\Components\Joins;

use Didslm\QueryBuilder\Components\Table;

class LeftJoin implements Join
{
    
    public function __construct(
        private string|Table $table, 
        private string $column, 
        private string $reference
    ){}

    public function getColumn(): string
    {
        return $this->column;
    }


    public function getTable(): string
    {
        return $this->table;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function toSql(): string
    {
        if ($this->table instanceof Table) {
            $column = $this->column;
            if ($this->table->hasAlias() && !str_contains($this->column, '.')) {
                $column = "{$this->table->getAlias()}.{$this->column}";
            }
            return "LEFT JOIN {$this->table->toSql()} ON {$column} = {$this->reference}";
        }
        return "LEFT JOIN {$this->table} ON {$this->column} = {$this->reference}";
    }
}
