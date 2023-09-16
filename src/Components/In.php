<?php

namespace Didslm\QueryBuilder\Components;

use Didslm\QueryBuilder\Utilities\Cleaner;

class In implements ConditionInterface
{
    private const DEFAULT_OPERATOR = 'IN';
    private string $field;
    private array $values;
    private string $operator;

    public function __construct(string $field, array $values, string $operator = self::DEFAULT_OPERATOR)
    {
        $this->field = $field;
        $this->values = $values;
        $this->operator = $operator;

        if (empty($values)) {
            throw new \InvalidArgumentException('Array cannot be empty');
            
        }

        if (!in_array($this->operator, self::ALL_OPERATORS)) {
            throw new \InvalidArgumentException(sprintf('Invalid operator %s', $this->operator));
        }
    }

    public static function with(string $field, array $values, string $operator = self::DEFAULT_OPERATOR): ConditionInterface
    {
        return new self($field, $values, $operator);
    }
    public function __toString(): string
    {
        return $this->toSql();
    }

    public function toSql(): string
    {
        $values = array_map(function($value) {
            if (is_numeric($value)) {
                return $value;
            } else {
                return sprintf("'%s'", Cleaner::escapeString($value));
            }
        }, $this->values);

        $values = implode(', ', $values);
        return sprintf('%s %s (%s)', $this->field, $this->operator, $values);
    }


    public function getColumn(): string
    {
        return $this->field;
    }

    public function getValue(): string
    {
        return implode(',', $this->values);
    }

    public function getOperator(): string
    {
        return $this->operator;
    }
}
