<?php

namespace Didslm\QueryBuilder\Components;

use Didslm\QueryBuilder\QueryBuilderInterface;

class Regex
{
    protected string $column;
    protected string $pattern;

    public function __construct(string $column, string $pattern)
    {
        $this->column = $column;
        $this->pattern = $pattern;
    }

    public function toSql(): string
    {
        return "{$this->column} REGEXP '{$this->pattern}'";
    }
}