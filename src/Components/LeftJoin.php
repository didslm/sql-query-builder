<?php

namespace Didslm\QueryBuilder\Components;

class LeftJoin
{
private string $table;
    private string $firstColumn;
    private string $secondColumn;

    public function __construct(string $table, string $firstColumn, string $secondColumn)
    {
        $this->table = $table;
        $this->firstColumn = $firstColumn;
        $this->secondColumn = $secondColumn;
    }

    public function toSql(): string
    {
        return sprintf('LEFT JOIN %s ON %s = %s', $this->table, $this->firstColumn, $this->secondColumn);
    }
}
