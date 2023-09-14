<?php

namespace Didslm\QueryBuilder\Components;

use Didslm\QueryBuilder\Utilities\Cleaner;

class Where implements Condition
{
    private const DEFAULT_OPERATOR = '=';
    private string $field;
    private string|float|int|null $value;
    private string $operator;

    public function __construct(string $field, string|float|int|null $value, ?string $operator = null)
    {

        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator ?? self::DEFAULT_OPERATOR;

        if (!in_array($this->operator, self::ALL_OPERATORS)) {
            throw new \InvalidArgumentException(sprintf('Invalid operator %s', $this->operator));
        }
    }

    public static function create(string $field, string $value, string $operator = self::DEFAULT_OPERATOR): Condition
    {
        return new self($field, $value, $operator);
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
}
