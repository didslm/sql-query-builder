<?php

namespace Didslm\QueryBuilder\Components;

class OrderBy
{
    protected $column;
    protected $direction;

    /**
     * Create a new OrderBy instance.
     *
     * @param string $column The name of the column to order by.
     * @param string $direction The direction to order by (ASC or DESC).
     */
    public function __construct(string $column, string $direction = 'ASC')
    {
        $this->column = $column;
        $this->direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
    }

    /**
     * Convert the order by clause to a SQL string.
     *
     * @return string
     */
    public function toSql(): string
    {
        return "{$this->column} {$this->direction}";
    }
}
