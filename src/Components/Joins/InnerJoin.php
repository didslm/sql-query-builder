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



    public function setParentTable(Table $table): void
    {
        $this->parentTable = $table;
    }

    public function toSql(): string
    {
        $column = AliasResolver::resolve($this->table, $this->column);
        $refrence = AliasResolver::resolve($this->parentTable, $this->foreignColumn);
        return sprintf('INNER JOIN %s ON %s = %s', $this->table->getTable(), $column, $refrence);
    }
}