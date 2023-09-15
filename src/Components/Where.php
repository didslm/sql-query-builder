<?php

namespace Didslm\QueryBuilder\Components;

use Didslm\QueryBuilder\Utilities\Cleaner;

class Where implements GroupCondition
{
    private const DEFAULT_OPERATOR = '=';
    private string $field;
    private string|float|int|null $value;
    private string $operator;

    private array $conditions = [];

    public function __construct(string $field, string|float|int|null $value, ?string $operator = null)
    {

        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator ?? self::DEFAULT_OPERATOR;

        if (!in_array($this->operator, self::ALL_OPERATORS)) {
            throw new \InvalidArgumentException(sprintf('Invalid operator %s', $this->operator));
        }
    }

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

    public function toSql(): string
    {
        $sql = $this->buildCondition();
        if (count($this->conditions) === 0) {
            return $sql;
        }

        foreach ($this->conditions as $condition) {
            $sql = sprintf('%s AND %s', $sql, $condition->toSql());
        }

        return sprintf('(%s)', $sql);
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

    public function __toString(): string
    {
        return $this->toSql();
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function addCondition(Condition $condition): self
    {
        $this->conditions[] = $condition;
        return $this;
    }
}
