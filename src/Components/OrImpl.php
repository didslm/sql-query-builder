<?php

namespace Didslm\QueryBuilder\Components;

use Didslm\QueryBuilder\Interface\ConditionInterface;
use Didslm\QueryBuilder\Interface\GroupConditionInterface;
use Didslm\QueryBuilder\Utilities\Cleaner;

class OrImpl extends AbstractCondition implements GroupConditionInterface
{
    private const DEFAULT_OPERATOR = '=';
    private array $conditions = [];

    public function __construct(
        protected string $field,
        protected string|float|int|null|array $value,
        ?string $operator = null
    ){
        $this->operator = $operator ?? self::DEFAULT_OPERATOR;
        if (!in_array($this->operator, self::ALL_OPERATORS)) {
            throw new \InvalidArgumentException(sprintf('Invalid operator %s', $this->operator));
        }
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
        $sql = $this->buildCondition();
        if (count($this->conditions) === 0) {
            return $sql;
        }

        foreach ($this->conditions as $condition) {
            $andOr = Where::orInstance($condition) ? 'OR' : 'AND';
            $sql = sprintf('%s %s %s', $sql, $andOr, $condition->toSql());
        }

        if(count($this->conditions) > 0) {
            return sprintf('(%s)', $sql);
        } else {
            return $sql;
        }
    }

    private function buildCondition(): string
    {
        if (is_null($this->value)) {
            return sprintf('%s %s', $this->field, self::DEFAULT_OPERATORS_FOR_NULL[$this->operator] ?? $this->operator);
        }

        if (is_numeric($this->value)) {
            return sprintf('%s %s %s', $this->field, $this->operator, $this->value);
        }

        if (str_contains($this->value, ':') && strlen($this->value) > 1) {
            return sprintf('%s %s %s', $this->field, $this->operator, Cleaner::escapeString($this->value));
        }
        return sprintf("%s %s '%s'", $this->field, $this->operator, Cleaner::escapeString($this->value));
    }
}
