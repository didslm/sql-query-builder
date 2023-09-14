<?php

namespace Didslm\QueryBuilder\Components;


class Regex implements Condition
{
    private const DEFAULT_OPERATOR = 'REGEXP';
    protected string $column;
    protected string $pattern;


    public function __construct(string $column, string $pattern)
    {
        $this->column = $column;
        $this->pattern = $pattern;
    }

    public static function create(string $column, string $pattern): Condition
    {
        return new self($column, $pattern);
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getOperator(): string
    {
        return self::DEFAULT_OPERATOR;
    }

    public function getValue(): string
    {
        return $this->pattern;
    }

    public function toSql(): string
    {
        $pattern = str_contains($this->pattern, ':') ? $this->pattern : sprintf("'%s'", $this->pattern);
        return sprintf(
            '%s %s %s',
            $this->column, self::DEFAULT_OPERATOR, $pattern);
    }

    public function __toString(): string
    {
        return $this->toSql();
    }
}