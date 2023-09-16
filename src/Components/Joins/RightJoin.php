<?php

namespace Didslm\QueryBuilder\Components\Joins;

use Didslm\QueryBuilder\Components\Table;
use Didslm\QueryBuilder\Utilities\AliasResolver;

class RightJoin implements Join
{
    private ?Table $parentTable = null;

    public function __construct(
        private string|Table $table,
        private string $column,
        private string $reference
    ){
        if (is_string($table)) {
            $this->table = new Table($table);
        }
    }

    public function setParentTable(Table $table): self
    {
        $this->parentTable = $table;
        return $this;
    }

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
        $column = AliasResolver::resolve($this->table, $this->column, true);
        $reference = AliasResolver::resolve($this->parentTable, $this->reference, true);

        return "RIGHT JOIN {$this->table->toSql()} ON {$column} = {$reference}";
    }
}
