<?php

namespace Didslm\QueryBuilder\Components;
use Didslm\QueryBuilder\Interface\ConditionInterface;


class Regex extends AbstractCondition implements ConditionInterface
{
    private const DEFAULT_OPERATOR = 'REGEXP';

    public function __construct(string $column, string $pattern)
    {
        $this->field = $column;
        $this->value = $pattern;
    }

    public static function create(string $column, string $pattern): ConditionInterface
    {
        return new self($column, $pattern);
    }

    public function getOperator(): string
    {
        return self::DEFAULT_OPERATOR;
    }

    public function toSql(): string
    {
        $pattern = str_contains($this->value, ':') ? $this->value : sprintf("'%s'", $this->value);
        return sprintf(
            '%s %s %s',
            $this->field, self::DEFAULT_OPERATOR, $pattern);
    }
}
