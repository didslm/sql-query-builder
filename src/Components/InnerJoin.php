<?php

namespace Didslm\QueryBuilder\Components;

class InnerJoin
{
    private string $table;
    private string $column;
    private string $foreignColumn;
    public function __construct(string $table, string $column, string $foreignColumn)
    {
        $this->table = $table;
        $this->column = $column;
        $this->foreignColumn = $foreignColumn;
    }

    public function toSql(): string
    {
        return sprintf('INNER JOIN %s ON %s = %s', $this->table, $this->column, $this->foreignColumn);
    }
}