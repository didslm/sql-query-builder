<?php

namespace Didslm\QueryBuilder\Components;

class OrGroup implements GroupCondition
{
    private const DEFAULT_OPERATOR = 'OR';
    private array $groupConditions = [];

    public static function create(ConditionInterface|GroupCondition ...$groups): OrGroup
    {
        $instance = new self();
        foreach ($groups as $group) {
            $instance->addGroupCondition($group);
        }
        return $instance;
    }


    public function addGroupCondition(ConditionInterface|GroupCondition $groupCondition): self
    {
        $this->groupConditions[] = $groupCondition;
        return $this;
    }

    public function toSql(): string
    {
        if (count($this->groupConditions) === 1) {
            $last = end($this->groupConditions);
            return $last->toSql();
        }

        $sql = implode(sprintf(' %s ', self::DEFAULT_OPERATOR), array_map(fn(ConditionInterface|GroupCondition $group) => $group->toSql(), $this->groupConditions));

        return sprintf('(%s)', $sql);
    }

    public function type(): string
    {
        return self::DEFAULT_OPERATOR;
    }

    public function __toString(): string
    {
        return $this->toSql();
    }
}
