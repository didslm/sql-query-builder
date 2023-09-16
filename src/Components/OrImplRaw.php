<?php

namespace Didslm\QueryBuilder\Components;

use Didslm\QueryBuilder\Interface\ConditionInterface;
use Didslm\QueryBuilder\Interface\GroupConditionInterface;

/**
 * An implementation for append raw or where.
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 */

class OrImplRaw extends AbstractCondition implements GroupConditionInterface
{
    private const DEFAULT_OPERATOR = '=';
    private array $conditions = [];

    public function __construct(
        string $value
    ){
        $this->value = $value;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function addCondition(ConditionInterface $condition): self
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public function toSql(): string
    {
        return $this->value;
    }
}
