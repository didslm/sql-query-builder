<?php

namespace Didslm\QueryBuilder\Components;

/**
 * Abstracttion class for Condition.
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 */

abstract class AbstractCondition
{
    protected string $field;
    protected string $value;
    protected string $operator;

    public function getColumn(): string
    {
        return $this->field;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function __toString(): string
    {
        return $this->toSql();
    }
}
