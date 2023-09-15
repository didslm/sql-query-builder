<?php declare(strict_types=1);

namespace Didslm\QueryBuilder\Components;
use Didslm\QueryBuilder\Interface\ConditionInterface;

/**
 * An implementation for NOT LIKE operator.
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 */

class NotLike extends AbstractCondition implements ConditionInterface {
    private const DEFAULT_OPERATOR = 'NOT LIKE';

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

    public function toSql(): string
    {
        $value = str_replace('*', '%', $this->value);
        if (str_contains($this->value, ':') && strlen($this->value) > 1) {
            return sprintf('%s %s %s', $this->field, $this->operator, $value);
        }

        return sprintf("%s %s '%s'", $this->field, $this->operator, $value);
    }
}
