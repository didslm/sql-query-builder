<?php

namespace Didslm\QueryBuilder\Components\Joins;

use Didslm\QueryBuilder\Components\Table;
use Didslm\QueryBuilder\Utilities\AliasResolver;

class InnerJoin implements Join
{
    private ?Table $parentTable = null;
    public function __construct(private string|Table $table, private string $column, private string $foreignColumn){
        if (is_string($table)) {
            $this->table = new Table($table);
        }
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getReference(): string
    {
        return $this->foreignColumn;
    }

    public function getTable(): string
    {
        return $this->table->getTable();
    }



    public function setParentTable(Table $table): self
    {
        $this->parentTable = $table;
        return $this;
    }

    public function toSql(): string
    {
        $column = AliasResolver::resolve($this->table, $this->column, true);
        $refrence = AliasResolver::resolve($this->parentTable, $this->foreignColumn, true);

        return "INNER JOIN {$this->table->toSql()} ON {$column} = {$refrence}";

    }
}
