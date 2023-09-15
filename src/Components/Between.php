<?php

namespace Didslm\QueryBuilder\Components;

use Didslm\QueryBuilder\Interface\ConditionInterface;
use Didslm\QueryBuilder\Utilities\Cleaner;

/**
 * An implementation for BETWEEN operator.
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 */
class Between extends AbstractCondition implements ConditionInterface
{
    private const DEFAULT_OPERATOR = 'BETWEEN';

    public function __construct(string $field, array $values, string $operator = self::DEFAULT_OPERATOR)
    {
        $this->field = $field;
        $this->value = $values;
        $this->operator = $operator;

        if (empty($values)) {
            throw new \InvalidArgumentException('Array cannot be empty');
        }

        if (count($values) !== 2) {
            throw new \InvalidArgumentException('The array is limited to only 2 index for its values.');
        }

        if ($values[0] > $values[1]) {
            throw new \InvalidArgumentException('The first array index value should be lower than the second array index value.');
        }

        if (!in_array($this->operator, self::ALL_OPERATORS)) {
            throw new \InvalidArgumentException(sprintf('Invalid operator %s', $this->operator));
        }
    }

    public static function with(string $field, array $values, string $operator = self::DEFAULT_OPERATOR): ConditionInterface
    {
        return new self($field, $values, $operator);
    }

    public function toSql(): string
    {
        return sprintf('%s %s %s', $this->field, $this->operator, $this->getValue());
    }

    public function getValue(): string
    {
        $values = array_map(function ($value) {
            if (is_numeric($value)) {
                return $value;
            } else {
                return sprintf("'%s'", Cleaner::escapeString($value));
            }
        }, $this->value);

        return implode(' AND ', $values);
    }
}
