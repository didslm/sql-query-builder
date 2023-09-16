<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Components;


class Like implements ConditionInterface {
    private const DEFAULT_OPERATOR = 'LIKE';

    private string $field;
    private string $value;
    private string $operator;

    public function __construct(string $field, string $value, string $operator = self::DEFAULT_OPERATOR)
    {
        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator;
    }

    public static function create(string $field, string $value, string $operator = self::DEFAULT_OPERATOR): ConditionInterface
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
        $value = str_replace('*', '%', $this->value);
        if (str_contains($this->value, ':') && strlen($this->value) > 1) {
            return sprintf('%s %s %s', $this->field, $this->operator, $value);
        }
        
        return sprintf("%s %s '%s'", $this->field, $this->operator, $value);
    }

    public function __toString(): string
    {
        return $this->toSql();
    }
}
