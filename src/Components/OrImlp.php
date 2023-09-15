<?php

namespace Didslm\QueryBuilder\Components;

use Didslm\QueryBuilder\Utilities\Cleaner;

class OrImlp implements GroupCondition
{
    private const DEFAULT_OPERATOR = '=';
    private array $conditions = [];

    public function __construct(
        private string $column,
        private mixed $value,
        private ?string $operator = null
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

    public function addCondition(Condition $condition): self
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
            $andOr = $condition instanceof OrImlp ? 'OR' : 'AND';
            $sql = sprintf('%s %s %s', $sql, $andOr, $condition->toSql());
        }

        return sprintf('(%s)', $sql);
    }

    public function getColumn(): string
    {
        return $this->column;
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

    private function buildCondition(): string
    {
        if (is_null($this->value)) {
            return sprintf('%s %s', $this->column, self::DEFAULT_OPERATORS_FOR_NULL[$this->operator] ?? $this->operator);
        }

        if (is_numeric($this->value)) {
            return sprintf('%s %s %s', $this->column, $this->operator, $this->value);
        }

        if (str_contains($this->value, ':') && strlen($this->value) > 1) {
            return sprintf('%s %s %s', $this->column, $this->operator, Cleaner::escapeString($this->value));
        }
        return sprintf("%s %s '%s'", $this->column, $this->operator, Cleaner::escapeString($this->value));
    }
}
