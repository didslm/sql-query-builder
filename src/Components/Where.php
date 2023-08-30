<?php

namespace Didslm\QueryBuilder\Components;

use Didslm\QueryBuilder\QueryBuilderInterface;

class Where implements QueryBuilderInterface
{
    private string $field;
    private string $value;
    private string $operator;

    public function __construct(string $field, string $value, string $operator = '=')
    {
        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator;
    }

    public function toSql(): string
    {
        return sprintf("%s %s '%s'", $this->field, $this->operator, $this->value);
    }
}