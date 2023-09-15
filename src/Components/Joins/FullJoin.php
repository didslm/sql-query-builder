<?php

namespace Didslm\QueryBuilder\Components\Joins;

use Didslm\QueryBuilder\Components\Table;
use Didslm\QueryBuilder\Utilities\AliasResolver;

/**
 * An implementation for Full Join
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 */

class FullJoin implements Join
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

    public function setParentTable(Table $table): void
    {
        $this->parentTable = $table;
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
        $refrence = AliasResolver::resolve($this->parentTable, $this->reference, true);

        return "FULL OUTER JOIN {$this->table->toSql()} ON {$column} = {$refrence}";
    }
}
