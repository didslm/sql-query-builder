<?php

namespace Didslm\QueryBuilder\Components;

use Didslm\QueryBuilder\Interface\ConditionInterface;
use Didslm\QueryBuilder\Interface\GroupConditionInterface;

/**
 * An implementation for append raw where.
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 */
class WhereRaw extends AbstractCondition implements GroupConditionInterface
{
    private array $conditions = [];

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function toSql(): string
    {
        return $this->value;
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
}
